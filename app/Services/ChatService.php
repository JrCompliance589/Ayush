<?php

namespace App\Services;

use App\Events\NewChatEvent;
use App\Http\Resources\ContactResource;
use App\Helpers\CustomHelper;
use App\Models\Addon;
use App\Models\Chat;
use App\Models\ChatLog;
use App\Models\ChatMedia;
use App\Models\ChatNote;
use App\Models\ChatTicket;
use App\Models\ChatTicketLog;
use App\Models\Contact;
use App\Models\ContactField;
use App\Models\ContactGroup;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\Team;
use App\Models\Template;
use App\Services\SubscriptionService;
use App\Services\WhatsappService;
use App\Traits\TemplateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\InboundChatsExport;
use Maatwebsite\Excel\Facades\Excel;
class ChatService
{
    use TemplateTrait;

    private $whatsappService;
    private $organizationId;

    public function __construct($organizationId)
    {
        $this->organizationId = $organizationId;
        $this->initializeWhatsappService();
    }

    private function initializeWhatsappService()
    {
        $organization = Organization::where('id', $this->organizationId)->first();
        if (!$organization) {
            Log::warning("Organization not found for ID: {$this->organizationId}");
            $config = [];
        } else {
            $config = $organization->metadata;
            $config = $config ? json_decode($config, true) : [];
        }

        $accessToken = $config['whatsapp']['access_token'] ?? null;
        $apiVersion = config('graph.api_version');
        $appId = $config['whatsapp']['app_id'] ?? null;
        $phoneNumberId = $config['whatsapp']['phone_number_id'] ?? null;
        $wabaId = $config['whatsapp']['waba_id'] ?? null;

        $this->whatsappService = new WhatsappService($accessToken, $apiVersion, $appId, $phoneNumberId, $wabaId, $this->organizationId);
    }

    public function getChatList($request, $uuid = null, $searchTerm = null)
    {
        $user = auth()->user();
        $role = null;
        // Scope teams to the current organization to get the correct role
        $user->load(['teams' => function ($query) {
            $query->where('organization_id', $this->organizationId);
        }]);
        if ($user && $user->teams && count($user->teams) > 0) {
            $role = $user->teams[0]->role;
        } else {
            Log::warning("User has no teams assigned", ['user_id' => $user?->id]);
        }
        
        $contact = new Contact;
        $unassigned = ChatTicket::where('assigned_to', NULL)->count();
        $closedCount = ChatTicket::where('status', 'closed')->count();
        $closedCount = ChatTicket::where('status', 'open')->count();
        $allCount = ChatTicket::count();
        $config = Organization::where('id', $this->organizationId)->first();
        $agents = Team::where('organization_id', $this->organizationId)->get();
        $ticketState = $request->status == null ? 'all' : $request->status;
        $sortDirection = $request->session()->get('chat_sort_direction') ?? 'desc';
        $allowAgentsToViewAllChats = true;
        $ticketingActive = false;
        $aimodule = CustomHelper::isModuleEnabled('AI Assistant');

        //Check if tickets module has been enabled
        if($config && $config->metadata != NULL){
            $settings = json_decode($config->metadata);

            if(isset($settings->tickets) && $settings->tickets->active === true){
                $ticketingActive = true;

                // Batch insert missing chat tickets - retry on deadlock
                $retries = 3;
                while ($retries > 0) {
                    try {
                        \DB::statement("
                            INSERT IGNORE INTO chat_tickets (contact_id, assigned_to, status, created_at, updated_at)
                            SELECT c.id, NULL, 'open', NOW(), NOW()
                            FROM contacts c
                            WHERE c.organization_id = ?
                            AND c.latest_chat_created_at IS NOT NULL
                            AND c.deleted_at IS NULL
                            AND NOT EXISTS (SELECT 1 FROM chat_tickets ct WHERE ct.contact_id = c.id)
                        ", [$this->organizationId]);
                        break;
                    } catch (\Illuminate\Database\QueryException $e) {
                        $retries--;
                        if ($retries === 0 || $e->getCode() !== '40001') {
                            Log::error('Chat ticket insert failed: ' . $e->getMessage());
                            break;
                        }
                        usleep(50000);
                    }
                }

                //Check if agents can view all chats
                $allowAgentsToViewAllChats = $settings->tickets->allow_agents_to_view_all_chats;
            }
        }

        // Retrieve the list of contacts with chats
        $contacts = $contact->contactsWithChats($this->organizationId, $searchTerm, $ticketingActive, $ticketState, $sortDirection, $role, $allowAgentsToViewAllChats);
        $rowCount = $contact->contactsWithChatsCount($this->organizationId, $searchTerm, $ticketingActive, $ticketState, $sortDirection, $role, $allowAgentsToViewAllChats);

        $pusherSettings = Setting::whereIn('key', [
            'pusher_app_id',
            'pusher_app_key',
            'pusher_app_secret',
            'pusher_app_cluster',
        ])->pluck('value', 'key')->toArray();

        $perPage = 10; // Number of items per page
        $totalContacts = count($contacts); // Total number of contacts
        $messageTemplates = Template::where('organization_id', $this->organizationId)
            ->where('deleted_at', null)
            ->where('status', 'APPROVED')
            ->get();

        if ($uuid !== null) {
            $contact = Contact::with(['lastChat', 'lastInboundChat', 'notes', 'contactGroups'])
                ->where('uuid', $uuid)
                ->first();

            if (!$contact) {
                Log::warning('Chat contact not found', ['uuid' => $uuid, 'userId' => auth()->id()]);
                $uuid = null; // Fall through to the no-contact branch
            }
        }

        if ($uuid !== null && $contact) {
            $ticket = ChatTicket::with('user')
                ->where('contact_id', $contact->id)
                ->first();

            $initialMessages = $this->getChatMessages($contact->id);

            // Mark messages as read
            Chat::where('contact_id', $contact->id)
                ->where('type', 'inbound')
                ->whereNull('deleted_at')
                ->where('is_read', 0)
                ->update(['is_read' => 1]);

            if (request()->expectsJson()) {
                return response()->json([
                    'result' => ContactResource::collection($contacts)->response()->getData(),
                ], 200);
            } else {
                $settings = json_decode($config->metadata);

                //To ensure the unread message counter is updated
                $unreadMessages = Chat::where('organization_id', $this->organizationId)
                    ->where('type', 'inbound')
                    ->where('deleted_at', NULL)
                    ->where('is_read', 0)
                    ->count();

                return Inertia::render('User/Chat/Index', [
                    'title' => 'Chats',
                    'rows' => ContactResource::collection($contacts),
                    'simpleForm' => CustomHelper::isModuleEnabled('AI Assistant') && optional(optional($settings)->ai)->ai_chat_form_active ? false : true,
                    'rowCount' => $rowCount,
                    'filters' => request()->all(),
                    'pusherSettings' => $pusherSettings,
                    'organizationId' => $this->organizationId,
                    'state' => app()->environment(),
                    'demoNumber' => env('DEMO_NUMBER'),
                    'settings' => $config,
                    'templates' => $messageTemplates,
                    'status' => $request->status ?? 'all',
                    'chatThread' => $initialMessages['messages'],
                    'hasMoreMessages' => $initialMessages['hasMoreMessages'],
                    'nextPage' => $initialMessages['nextPage'],
                    'contact' => $contact,
                    'fields' => ContactField::where('organization_id', $this->organizationId)->where('deleted_at', null)->get(),
                    'locationSettings' => $this->getLocationSettings(),
                    'ticket' => $ticket,
                    'agents' => $agents,
                    'addon' => $aimodule,
                    'chat_sort_direction' => $sortDirection,
                    'unreadMessages' => $unreadMessages,
                    'isChatLimitReached' => SubscriptionService::isSubscriptionFeatureLimitReached($this->organizationId, 'message_limit')
                ]);
            }
        }

        if (request()->expectsJson()) {
            return response()->json([
                'result' => ContactResource::collection($contacts)->response()->getData(),
            ], 200);
        } else {
            $settings = json_decode($config->metadata);
            
            return Inertia::render('User/Chat/Index', [
                'title' => 'Chats',
                'rows' => ContactResource::collection($contacts),
                'simpleForm' => !CustomHelper::isModuleEnabled('AI Assistant') || empty($settings->ai->ai_chat_form_active),
                'rowCount' => $rowCount,
                'filters' => request()->all(),
                'pusherSettings' => $pusherSettings,
                'organizationId' => $this->organizationId,
                'state' => app()->environment(),
                'settings' => $config,
                'templates' => $messageTemplates,
                'status' => $request->status ?? 'all',
                'agents' => $agents,
                'addon' => $aimodule,
                'ticket' => array(),
                'chat_sort_direction' => $sortDirection,
                'isChatLimitReached' => SubscriptionService::isSubscriptionFeatureLimitReached($this->organizationId, 'message_limit')
            ]);
        }
    }

    public function handleTicketAssignment($contactId){
        Log::info("handleTicketAssignment started", ['contactId' => $contactId, 'organizationId' => $this->organizationId]);
        
        $organizationId = $this->organizationId;
        $organization = Organization::where('id', $this->organizationId)->first();
        if (!$organization) {
            Log::warning("Organization not found in handleTicketAssignment for ID: {$this->organizationId}");
            return;
        }
        $settings = json_decode($organization->metadata);
        Log::info("Organization settings loaded", ['hasTickets' => isset($settings->tickets)]);

        // Check if ticket functionality is active
        if(isset($settings->tickets) && $settings->tickets->active === true){
            Log::info("Ticket functionality is active");
            $autoassignment = $settings->tickets->auto_assignment;
            $reassignOnReopen = $settings->tickets->reassign_reopened_chats;
            Log::info("Ticket settings", ['autoassignment' => $autoassignment, 'reassignOnReopen' => $reassignOnReopen]);

            // Check if a ticket already exists for the contact
            $ticket = ChatTicket::where('contact_id', $contactId)->first();
            Log::info("Ticket check", ['ticketExists' => $ticket !== null, 'ticketId' => $ticket?->id]);

            DB::transaction(function () use ($reassignOnReopen, $autoassignment, $ticket, $contactId, $organizationId) {
                if(!$ticket){
                    Log::info("Creating new ticket for contact", ['contactId' => $contactId]);
                    // Create a new ticket if it doesn't exist
                    $ticket = New ChatTicket;
                    $ticket->contact_id = $contactId;
                    $ticket->status = 'open';
                    $ticket->updated_at = now();

                    // Perform auto-assignment if enabled
                    if($autoassignment){
                        Log::info("Auto-assignment enabled, fetching agents");
                        
                        // Find agents only (exclude managers)
                        $agents = Team::where('organization_id', $organizationId)
                            ->where('role', 'agent')
                            ->withCount('tickets')
                            ->whereNull('deleted_at')
                            ->orderBy('tickets_count')
                            ->get();

                        Log::info("Agents retrieved", [
                            'totalAgents' => $agents->count(),
                            'agentDetails' => $agents->map(function($agent) {
                                return [
                                    'id' => $agent->id,
                                    'user_id' => $agent->user_id,
                                    'role' => $agent->role,
                                    'tickets_count' => $agent->tickets_count
                                ];
                            })->toArray()
                        ]);

                        if($agents->count() > 0){
                            // Get the minimum ticket count
                            $minTickets = $agents->min('tickets_count');
                            Log::info("Minimum tickets found", ['minTickets' => $minTickets]);
                            
                            // Get all agents with minimum tickets
                            $agentsWithMinTickets = $agents->filter(function($agent) use ($minTickets) {
                                return $agent->tickets_count === $minTickets;
                            })->values();
                            
                            Log::info("Agents with minimum tickets", [
                                'count' => $agentsWithMinTickets->count(),
                                'agentUserIds' => $agentsWithMinTickets->pluck('user_id')->toArray()
                            ]);
                            
                            // Randomly select one agent from those with minimum tickets
                            $selectedAgent = $agentsWithMinTickets->random();
                            Log::info("Agent selected for assignment", [
                                'selectedAgentId' => $selectedAgent->id,
                                'selectedAgentUserId' => $selectedAgent->user_id,
                                'ticketsCount' => $selectedAgent->tickets_count
                            ]);
                            $ticket->assigned_to = $selectedAgent->user_id;
                        } else {
                            // No agents found, don't assign to anybody
                            Log::warning("No agents found for auto-assignment", ['organizationId' => $organizationId]);
                            $ticket->assigned_to = NULL;
                        }
                    } else {
                        Log::info("Auto-assignment disabled, leaving ticket unassigned");
                        $ticket->assigned_to = NULL;
                    }

                    $ticket->save();
                    Log::info("New ticket created and saved", [
                        'ticketId' => $ticket->id,
                        'contactId' => $contactId,
                        'assignedTo' => $ticket->assigned_to,
                        'status' => $ticket->status
                    ]);

                    $ticketId = ChatTicketLog::insertGetId([
                        'contact_id' => $contactId,
                        'description' => 'Conversation was opened',
                        'created_at' => now()
                    ]);
                    Log::info("Ticket log created", ['ticketLogId' => $ticketId]);

                    ChatLog::insert([
                        'contact_id' => $contactId,
                        'entity_type' => 'ticket',
                        'entity_id' => $ticketId,
                        'created_at' => now()
                    ]);
                } else {
                    Log::info("Ticket already exists, checking if reopening is needed", [
                        'ticketId' => $ticket->id,
                        'currentStatus' => $ticket->status,
                        'assignedTo' => $ticket->assigned_to
                    ]);
                    
                    // Reopen the ticket if it's closed and reassignment on reopen is enabled
                    if($ticket->status === 'closed'){
                        Log::info("Ticket is closed, checking reassign on reopen setting", ['reassignOnReopen' => $reassignOnReopen]);
                        if($reassignOnReopen){
                            Log::info("Reassigning ticket on reopen");
                            
                            if($autoassignment){
                                Log::info("Auto-assignment enabled for reopened ticket");
                                
                                // Find agents only (exclude managers)
                                $agents = Team::where('organization_id', $organizationId)
                                    ->where('role', 'agent')
                                    ->withCount('tickets')
                                    ->whereNull('deleted_at')
                                    ->orderBy('tickets_count')
                                    ->get();

                                Log::info("Agents retrieved for reopened ticket", [
                                    'totalAgents' => $agents->count(),
                                    'agentDetails' => $agents->map(function($agent) {
                                        return [
                                            'id' => $agent->id,
                                            'user_id' => $agent->user_id,
                                            'tickets_count' => $agent->tickets_count
                                        ];
                                    })->toArray()
                                ]);

                                if($agents->count() > 0){
                                    // Get the minimum ticket count
                                    $minTickets = $agents->min('tickets_count');
                                    Log::info("Minimum tickets for reopened ticket", ['minTickets' => $minTickets]);
                                    
                                    // Get all agents with minimum tickets
                                    $agentsWithMinTickets = $agents->filter(function($agent) use ($minTickets) {
                                        return $agent->tickets_count === $minTickets;
                                    })->values();
                                    
                                    Log::info("Agents with minimum tickets for reopened ticket", [
                                        'count' => $agentsWithMinTickets->count(),
                                        'agentUserIds' => $agentsWithMinTickets->pluck('user_id')->toArray()
                                    ]);
                                    
                                    // Randomly select one agent from those with minimum tickets
                                    $selectedAgent = $agentsWithMinTickets->random();
                                    Log::info("Agent reassigned for reopened ticket", [
                                        'oldAssignedTo' => $ticket->assigned_to,
                                        'newAssignedTo' => $selectedAgent->user_id,
                                        'selectedAgentTicketsCount' => $selectedAgent->tickets_count
                                    ]);
                                    $ticket->assigned_to = $selectedAgent->user_id;
                                } else {
                                    // No agents found, don't assign to anybody
                                    Log::warning("No agents found for reopened ticket reassignment", ['organizationId' => $organizationId]);
                                    $ticket->assigned_to = NULL;
                                }
                            } else {
                                Log::info("Auto-assignment disabled for reopened ticket, leaving unassigned");
                                $ticket->assigned_to = NULL;
                            }
                        } else {
                            Log::info("Reassign on reopen disabled, keeping current assignment");
                        }

                        $ticket->status = 'open';
                        $ticket->save();

                        $ticketId = ChatTicketLog::insertGetId([
                            'contact_id' => $contactId,
                            'description' => 'Conversation was moved from closed to open',
                            'created_at' => now()
                        ]);
    
                        ChatLog::insert([
                            'contact_id' => $contactId,
                            'entity_type' => 'ticket',
                            'entity_id' => $ticketId,
                            'created_at' => now()
                        ]);
                    }
                }
            });
        }
    }

    public function sendMessage(object $request)
    {
        if($request->type === 'text'){
            return $this->whatsappService->sendMessage($request->uuid, $request->message, auth()->user()->id);
        } else {
            $storage = Setting::where('key', 'storage_system')->first()->value;
            $fileName = $request->file('file')->getClientOriginalName();
            $fileContent = $request->file('file');

            if($storage === 'local'){
                $location = 'local';
                $file = Storage::disk('local')->put('public', $fileContent);
                $mediaFilePath = $file;
                $mediaUrl = rtrim(config('app.url'), '/') . '/media/' . ltrim($mediaFilePath, '/');
            } else if($storage === 'aws') {
                $location = 'amazon';
                $file = $request->file('file');
                $filePath = 'uploads/media/received/'  . $this->organizationId . '/' . $fileName;
                $uploadedFile = $file->store('uploads/media/sent/' . $this->organizationId, 's3');
                $mediaFilePath = Storage::disk('s3')->url($uploadedFile);
                $mediaUrl = $mediaFilePath;
            }
    
            $this->whatsappService->sendMedia($request->uuid, $request->type, $fileName, $mediaFilePath, $mediaUrl, $location);
        }
    }

    public function sendTemplateMessage(object $request, $uuid)
    {
        $template = Template::where('uuid', $request->template)->first();
        $contact = Contact::where('uuid', $uuid)->first();
        $mediaId = null;

        if(in_array($request->header['format'], ['IMAGE', 'DOCUMENT', 'VIDEO'])){
            $header = $request->header;
            
            if ($request->header['parameters']) {
                $metadata['header']['format'] = $header['format'];
                $metadata['header']['parameters'] = [];
        
                foreach ($request->header['parameters'] as $key => $parameter) {
                    if ($parameter['selection'] === 'upload') {
                        $storage = Setting::where('key', 'storage_system')->first()->value;
                        $fileName = $parameter['value']->getClientOriginalName();
                        $fileContent = $parameter['value'];

                        if($storage === 'local'){
                            $file = Storage::disk('local')->put('public', $fileContent);
                            $mediaFilePath = $file;
            
                            $mediaUrl = rtrim(config('app.url'), '/') . '/media/' . ltrim($mediaFilePath, '/');
                        } else if($storage === 'aws') {
                            $file = $parameter['value'];
                            $uploadedFile = $file->store('uploads/media/sent/' . $this->organizationId, 's3');
                            $mediaFilePath = Storage::disk('s3')->url($uploadedFile);
            
                            $mediaUrl = $mediaFilePath;
                        }

                        $contentType = $this->getContentTypeFromUrl($mediaUrl);
                        $mediaSize = $this->getMediaSizeInBytesFromUrl($mediaUrl);

                        //save media
                        $chatMedia = new ChatMedia;
                        $chatMedia->name = $fileName;
                        $chatMedia->location = $storage == 'aws' ? 'amazon' : 'local';
                        $chatMedia->path = $mediaUrl;
                        $chatMedia->type = $contentType;
                        $chatMedia->size = $mediaSize;
                        $chatMedia->created_at = now();
                        $chatMedia->save();

                        $mediaId = $chatMedia->id;
                    } else {
                        $mediaUrl = $parameter['value'];
                    }
        
                    $metadata['header']['parameters'][] = [
                        'type' => $parameter['type'],
                        'selection' => $parameter['selection'],
                        'value' => $mediaUrl,
                    ];
                }
            }
        } else {
            $metadata['header'] = $request->header;
        }

        $metadata['body'] = $request->body;
        $metadata['footer'] = $request->footer;
        $metadata['buttons'] = $request->buttons;
        $metadata['media'] = $mediaId;

        //Build Template to send
        $template = $this->buildTemplate($template->name, $template->language, json_decode(json_encode($metadata)), $contact);
        
        return $this->whatsappService->sendTemplateMessage($contact->uuid, $template, auth()->user()->id, NULL, $mediaId);
    }

    public function clearMessage($uuid)
    {
        Chat::where('uuid', $uuid)
            ->update([
                'deleted_by' => auth()->user()->id,
                'deleted_at' => now()
            ]);
    }

    public function clearContactChat($uuid)
    {
        $contact = Contact::with('lastChat')->where('uuid', $uuid)->firstOrFail();
        Chat::where('contact_id', $contact->id)->update([
            'deleted_by' => auth()->user()->id,
            'deleted_at' => now()
        ]);

        ChatLog::where('contact_id', $contact->id)->where('entity_type', 'chat')->update([
            'deleted_by' => auth()->user()->id,
            'deleted_at' => now()
        ]);

        $chat = Chat::with('contact','media')->where('id', $contact->lastChat->id)->first();

        //event(new NewChatEvent($chat, $contact->organization_id));
    }

    private function getContentTypeFromUrl($url) {
        try {
            // Make a HEAD request to fetch headers only
            $response = Http::head($url);
    
            // Check if the Content-Type header is present
            if ($response->hasHeader('Content-Type')) {
                return $response->header('Content-Type');
            }
    
            return null;
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error fetching headers: ' . $e->getMessage());
            return null;
        }
    }

    private function getMediaSizeInBytesFromUrl($url) {
        $url = ltrim($url, '/');
        $imageContent = file_get_contents($url);
    
        if ($imageContent !== false) {
            return strlen($imageContent);
        }
    
        return null;
    }

    private function getLocationSettings(){
        // Retrieve the settings for the current organization
        $settings = Organization::where('id', $this->organizationId)->first();

        if ($settings) {
            // Decode the JSON metadata column into an associative array
            $metadata = json_decode($settings->metadata, true);

            if (isset($metadata['contacts'])) {
                // If the 'contacts' key exists, retrieve the 'location' value
                $location = $metadata['contacts']['location'];

                // Now, you have the location value available
                return $location;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getChatMessages($contactId, $page = 1, $perPage = 10)
    {
        $chatLogs = ChatLog::where('contact_id', $contactId)
            ->where('deleted_at', null)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // OPTIMIZED: Pre-load all related entities in batch to avoid N+1 queries
        $chatIds = [];
        $ticketLogIds = [];
        $noteIds = [];
        
        foreach ($chatLogs as $chatLog) {
            switch ($chatLog->entity_type) {
                case 'chat':
                    $chatIds[] = $chatLog->entity_id;
                    break;
                case 'ticket':
                    $ticketLogIds[] = $chatLog->entity_id;
                    break;
                case 'notes':
                    $noteIds[] = $chatLog->entity_id;
                    break;
            }
        }
        
        // Batch load all entities
        $chatsMap = !empty($chatIds) ? Chat::with('media', 'user', 'logs')->whereIn('id', $chatIds)->get()->keyBy('id') : collect();
        $ticketLogsMap = !empty($ticketLogIds) ? ChatTicketLog::whereIn('id', $ticketLogIds)->get()->keyBy('id') : collect();
        $notesMap = !empty($noteIds) ? ChatNote::whereIn('id', $noteIds)->get()->keyBy('id') : collect();

        $chats = [];
        foreach ($chatLogs as $chatLog) {
            $relatedEntity = null;
            switch ($chatLog->entity_type) {
                case 'chat':
                    $relatedEntity = $chatsMap->get($chatLog->entity_id);
                    break;
                case 'ticket':
                    $relatedEntity = $ticketLogsMap->get($chatLog->entity_id);
                    break;
                case 'notes':
                    $relatedEntity = $notesMap->get($chatLog->entity_id);
                    break;
            }
            
            $chats[] = array([
                'type' => $chatLog->entity_type,
                'value' => $relatedEntity
            ]);
        }

        return [
            'messages' => array_reverse($chats),
            'hasMoreMessages' => $chatLogs->hasMorePages(),
            'nextPage' => $chatLogs->currentPage() + 1
        ];
    }
    
    
        public function getInboundChats($request, $uuid = null, $searchTerm = null)
    {
        $days = $request->input('days', 1); // default = last 1 day

        // ✅ If Excel export requested, return download immediately
        if ($request->has('export') && $request->export === 'excel') {
            return Excel::download(
                new InboundChatsExport($days, $this->organizationId),
                "inbound_chats_last_{$days}_days.xlsx"
            );
        }

        $user = auth()->user();
        $role = null;
        // Scope teams to the current organization to get the correct role
        $user->load(['teams' => function ($query) {
            $query->where('organization_id', $this->organizationId);
        }]);
        if ($user && $user->teams && count($user->teams) > 0) {
            $role = $user->teams[0]->role;
        } else {
            Log::warning("User has no teams assigned in getInboundChats", ['user_id' => $user?->id]);
        }
        
        $contact = new Contact;
        $unassigned = ChatTicket::where('assigned_to', NULL)->count();
        $closedCount = ChatTicket::where('status', 'closed')->count();
        $closedCount = ChatTicket::where('status', 'open')->count();
        $allCount = ChatTicket::count();
        $config = Organization::where('id', $this->organizationId)->first();
        
        // Guard against missing organization
        if (!$config) {
            Log::warning("Organization not found for ID: {$this->organizationId}");
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Organization not found'], 404);
            }
            return redirect()->route('login')->with('error', 'Organization not found. Please login again.');
        }
        
        $agents = Team::where('organization_id', $this->organizationId)->get();
        $ticketState = $request->status == null ? 'all' : $request->status;
        $sortDirection = $request->session()->get('chat_sort_direction') ?? 'desc';
        $allowAgentsToViewAllChats = true;
        $ticketingActive = false;
        $aimodule = CustomHelper::isModuleEnabled('AI Assistant');

        //Check if tickets module has been enabled
        if ($config && $config->metadata != NULL) {
            $settings = json_decode($config->metadata);

            if (isset($settings->tickets) && $settings->tickets->active === true) {
                $ticketingActive = true;

                // Batch insert missing chat tickets - retry on deadlock
                $retries = 3;
                while ($retries > 0) {
                    try {
                        \DB::statement("
                            INSERT IGNORE INTO chat_tickets (contact_id, assigned_to, status, created_at, updated_at)
                            SELECT c.id, NULL, 'open', NOW(), NOW()
                            FROM contacts c
                            WHERE c.organization_id = ?
                            AND c.latest_chat_created_at IS NOT NULL
                            AND c.deleted_at IS NULL
                            AND NOT EXISTS (SELECT 1 FROM chat_tickets ct WHERE ct.contact_id = c.id)
                        ", [$this->organizationId]);
                        break;
                    } catch (\Illuminate\Database\QueryException $e) {
                        $retries--;
                        if ($retries === 0 || $e->getCode() !== '40001') {
                            Log::error('Chat ticket insert failed: ' . $e->getMessage());
                            break;
                        }
                        usleep(50000);
                    }
                }

                //Check if agents can view all chats
                $allowAgentsToViewAllChats = $settings->tickets->allow_agents_to_view_all_chats;
            }
        }

        // Retrieve the list of contacts with chats
        $contacts = $contact->contactsWithChats($this->organizationId, $searchTerm, $ticketingActive, $ticketState, $sortDirection, $role, $allowAgentsToViewAllChats);
        $rowCount = $contact->contactsWithChatsCount($this->organizationId, $searchTerm, $ticketingActive, $ticketState, $sortDirection, $role, $allowAgentsToViewAllChats);
        // Log::info('rowCount ' . json_encode($rowCount));

        $pusherSettings = Setting::whereIn('key', [
            'pusher_app_id',
            'pusher_app_key',
            'pusher_app_secret',
            'pusher_app_cluster',
        ])->pluck('value', 'key')->toArray();

        $perPage = 10; // Number of items per page
        $totalContacts = count($contacts); // Total number of contacts
        $messageTemplates = Template::where('organization_id', $this->organizationId)
            ->where('deleted_at', null)
            ->where('status', 'APPROVED')
            ->get();

        if ($uuid !== null) {
            // If $uuid is provided, get the chat thread for a specific contact
            $contact = Contact::with(['lastChat', 'lastInboundChat', 'notes', 'contactGroups'])->where('uuid', $uuid)->first();
            $ticket = ChatTicket::with('user')->where('contact_id', $contact->id)->first();
            $chatLogs = ChatLog::where('contact_id', $contact->id)->where('deleted_at', null)->get();
            
            // OPTIMIZED: Pre-load all related entities in batch to avoid N+1 queries
            $chatIds = [];
            $ticketLogIds = [];
            $noteIds = [];
            
            foreach ($chatLogs as $chatLog) {
                switch ($chatLog->entity_type) {
                    case 'chat':
                        $chatIds[] = $chatLog->entity_id;
                        break;
                    case 'ticket':
                        $ticketLogIds[] = $chatLog->entity_id;
                        break;
                    case 'notes':
                        $noteIds[] = $chatLog->entity_id;
                        break;
                }
            }
            
            // Batch load all entities
            $chatsMap = !empty($chatIds) ? Chat::with('media', 'user', 'logs')->whereIn('id', $chatIds)->get()->keyBy('id') : collect();
            $ticketLogsMap = !empty($ticketLogIds) ? ChatTicketLog::whereIn('id', $ticketLogIds)->get()->keyBy('id') : collect();
            $notesMap = !empty($noteIds) ? ChatNote::whereIn('id', $noteIds)->get()->keyBy('id') : collect();

            $chats = [];
            foreach ($chatLogs as $chatLog) {
                $relatedEntity = null;
                switch ($chatLog->entity_type) {
                    case 'chat':
                        $relatedEntity = $chatsMap->get($chatLog->entity_id);
                        break;
                    case 'ticket':
                        $relatedEntity = $ticketLogsMap->get($chatLog->entity_id);
                        break;
                    case 'notes':
                        $relatedEntity = $notesMap->get($chatLog->entity_id);
                        break;
                }
                
                $chats[] = array([
                    'type' => $chatLog->entity_type,
                    'value' => $relatedEntity
                ]);
            }

            //Mark all chats as read
            Chat::where('contact_id', $contact->id)
                ->where('type', 'inbound')
                ->whereNull('deleted_at')
                ->where('is_read', 0)
                ->update([
                    'is_read' => 1
                ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'result' => ContactResource::collection($contacts)->response()->getData(),
                ], 200);
            } else {
                $settings = json_decode($config->metadata);

                //To ensure the unread message counter is updated
                $unreadMessages = Chat::where('organization_id', $this->organizationId)
                    ->where('type', 'inbound')
                    ->where('deleted_at', NULL)
                    ->where('is_read', 0)
                    ->count();

                return Inertia::render('User/Chat/Index', [
                    'title' => 'Chats',
                    'rows' => ContactResource::collection($contacts),
                    'simpleForm' => CustomHelper::isModuleEnabled('AI Assistant') && optional(optional($settings)->ai)->ai_chat_form_active ? false : true,
                    'rowCount' => $rowCount,
                    'filters' => request()->all(),
                    'pusherSettings' => $pusherSettings,
                    'organizationId' => $this->organizationId,
                    'state' => app()->environment(),
                    'demoNumber' => env('DEMO_NUMBER'),
                    'settings' => $config,
                    'templates' => $messageTemplates,
                    'status' => $request->status ?? 'all',
                    'chatThread' => $chats,
                    'contact' => $contact,
                    'fields' => ContactField::where('organization_id', $this->organizationId)->where('deleted_at', null)->get(),
                    'locationSettings' => $this->getLocationSettings(),
                    'ticket' => $ticket,
                    'agents' => $agents,
                    'addon' => $aimodule,
                    'chat_sort_direction' => $sortDirection,
                    'unreadMessages' => $unreadMessages,
                    'isChatLimitReached' => SubscriptionService::isSubscriptionFeatureLimitReached($this->organizationId, 'message_limit')
                ]);
            }
        }

        if (request()->expectsJson()) {
            return response()->json([
                'result' => ContactResource::collection($contacts)->response()->getData(),
            ], 200);
        } else {
            $settings = json_decode($config->metadata);

            return Inertia::render('User/Chat/Index', [
                'title' => 'Chats',
                'rows' => ContactResource::collection($contacts),
                'simpleForm' => !CustomHelper::isModuleEnabled('AI Assistant') || empty($settings->ai->ai_chat_form_active),
                'rowCount' => $rowCount,
                'filters' => request()->all(),
                'pusherSettings' => $pusherSettings,
                'organizationId' => $this->organizationId,
                'state' => app()->environment(),
                'settings' => $config,
                'templates' => $messageTemplates,
                'status' => $request->status ?? 'all',
                'agents' => $agents,
                'addon' => $aimodule,
                'ticket' => array(),
                'chat_sort_direction' => $sortDirection,
                'isChatLimitReached' => SubscriptionService::isSubscriptionFeatureLimitReached($this->organizationId, 'message_limit')
            ]);
        }
    }
}