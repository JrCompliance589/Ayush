<?php

namespace App\Http\Controllers;

use App\Events\NewChatEvent;
use App\Helpers\WebhookHelper;
use App\Http\Controllers\Controller as BaseController;
use App\Models\AutoReply;
use App\Models\Contact;
use App\Models\Chat;
use App\Models\ChatLog;
use App\Models\ChatStatusLog;
use App\Models\ChatMedia;
use App\Models\CampaignLog;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\Template;
use App\Resolvers\PaymentPlatformResolver;
use App\Services\AutoReplyService;
use App\Services\ChatService;
use App\Services\PhoneService;
use App\Services\StripeService;
use App\Services\SubscriptionService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Propaganistas\LaravelPhone\PhoneNumber;
use Str;

class WebhookController extends BaseController
{
    protected $paymentPlatformResolver;

    public function __construct()
    {
        $this->paymentPlatformResolver = new PaymentPlatformResolver();

        Config::set('broadcasting.connections.pusher', [
            'driver' => 'pusher',
            'key' => Setting::where('key', 'pusher_app_key')->value('value'),
            'secret' => Setting::where('key', 'pusher_app_secret')->value('value'),
            'app_id' => Setting::where('key', 'pusher_app_id')->value('value'),
            'options' => [
                'cluster' => Setting::where('key', 'pusher_app_cluster')->value('value'),
            ],
        ]);
    }

    public function whatsappWebhook(Request $request){
        //Log::info($request);

        // Forward a copy of the request to external webhook
        try {
            $client = new Client(['timeout' => 5, 'connect_timeout' => 3]);
            $forwardUrl = 'https://intermetatarsal-bucolically-damon.ngrok-free.dev/api/v1/whatsapp/webhook';

            if ($request->isMethod('get')) {
                $client->getAsync($forwardUrl . '?' . http_build_query($request->query()));
            } else {
                $client->postAsync($forwardUrl, [
                    'headers' => [
                        'Content-Type' => $request->header('Content-Type', 'application/json'),
                    ],
                    'body' => $request->getContent(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Webhook forward failed: ' . $e->getMessage());
        }

        $verifyToken = Setting::where('key', 'whatsapp_callback_token')->first()->value;

        $mode = $request->input('hub_mode');
        $token = $request->input('hub_verify_token');
        $challenge = $request->input('hub_challenge');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return Response::make($challenge, 200);
        } else {
            return Response::json(['error' => 'Forbidden'], 200);
        }
    }

    public function handle(Request $request, $identifier = null)
    {
        //Log::info('Webhook Handler: Start processing for identifier ' . $identifier);
        $organization = $this->getOrganizationByIdentifier($identifier);

        if (!$organization) {
            return $this->forbiddenResponse();
        }

        return $this->handleMethod($request, $organization);
    }

    protected function getOrganizationByIdentifier($identifier)
    {
        return Organization::where('identifier', $identifier)->first();
    }

    protected function handleMethod(Request $request, Organization $organization)
    {
        if ($request->isMethod('get')) {
            return $this->handleGetRequest($request, $organization);
        } elseif ($request->isMethod('post')) {
            $metadata = json_decode($organization->metadata);

            if (empty($metadata)) {
                return $this->forbiddenResponse();
            }

            /*$appSecret = $metadata->whatsapp->app_secret;
            $headerSignature = $request->header('X-Hub-Signature-256');
            $payload = $request->getContent();
            $calculatedSignature = 'sha256=' . hash_hmac('sha256', $payload, $appSecret);

            if (!$this->isValidSignature($calculatedSignature, $headerSignature)) {
                return $this->invalidSignatureResponse();
            }*/

            return $this->handlePostRequest($request, $organization);
        }

        return Response::json(['error' => 'Method Not Allowed'], 405);
    }

    protected function forbiddenResponse()
    {
        return Response::json(['error' => 'Forbidden'], 403);
    }

    protected function isValidSignature($calculatedSignature, $headerSignature)
    {
        return hash_equals($calculatedSignature, $headerSignature);
    }

    protected function invalidSignatureResponse()
    {
        return Response::json(['status' => 'error', 'message' => __('Invalid payload signature')], 400);
    }

    protected function handleGetRequest(Request $request, Organization $organization)
    {
        try {
            $verifyToken = $organization->identifier;

            $mode = $request->input('hub_mode');
            $token = $request->input('hub_verify_token');
            $challenge = $request->input('hub_challenge');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                return Response::make($challenge, 200);
            } else {
                return Response::json(['error' => 'Forbidden'], 404);
            }
        } catch (\Exception $e) {
            Log::error("Error processing webhook: " . $e->getMessage());
            return Response::json(['error' => $e->getMessage()], 403);
        }
    }

    protected function handlePostRequest(Request $request, Organization $organization)
    {
        $res = $request->entry[0]['changes'][0];

        //Log::info($request);

        if($res['field'] === 'messages'){
            $contacts = $res['value']['contacts'][0] ?? null;
            $messages = $res['value']['messages'] ?? null;
            $statuses = $res['value']['statuses'] ?? null;

            if($statuses) {
                //$response = $res['value']['statuses'][0];
                foreach($statuses as $response){
                    $chatWamId = $response['id'];
                    $status = $response['status'];

                    $chat = Chat::where('wam_id', $chatWamId)->first();

                    if($chat){
                        $chat->status = $status;
                        $chat->save();

                        $chatStatusLog = new ChatStatusLog;
                        $chatStatusLog->chat_id = $chat->id;
                        $chatStatusLog->metadata = json_encode($response);
                        $chatStatusLog->save();

                        // If chat status is failed, check if it's linked to a campaign and handle failed contact group
                        if ($status === 'failed') {
                            $this->handleFailedCampaignChat($chat, $organization);
                        }
                    }
                }

                // Trigger webhook
                WebhookHelper::triggerWebhookEvent('message.status.update', [
                    'data' => $res,
                ], $organization->id);
            } else if($messages) {
                $isLimitReached = SubscriptionService::isSubscriptionLimitReachedForInboundMessages($organization->id);
                //Log::info($messages);

                if(!$isLimitReached){
                    foreach($messages as $response){
                        $phone = $response['from'];

                        if (substr($phone, 0, 1) !== '+') {
                            $phone = '+' . $phone;
                        }

                        // Use PhoneService for Brazilian number validation and formatting
                        $phone = PhoneService::getE164Format($phone);

                        //Check if contact exists in organization
                        $contact = Contact::where('organization_id', $organization->id)->where('phone', $phone)->whereNull('deleted_at')->first();
                        $isNewContact = false;

                        if(!$contact){
                            //Create a contact
                            $contactData = $res['value']['contacts'][0]['profile'] ?? null;

                            $contact = Contact::create([
                                'first_name' => $contactData['name'] ?? null,
                                'last_name' => null,
                                'email' => null,
                                'phone' => $phone,
                                'organization_id' => $organization->id,
                                'created_by' => 0,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            $isNewContact = true;
                        }

                        if($contact){
                            if($contact->first_name == NULL){
                                $contactData = $res['value']['contacts'][0]['profile'] ?? null;
                                if($contactData && isset($contactData['name'])){
                                    $contact->update([
                                        'first_name' => $contactData['name'],
                                    ]);
                                }
                            }

                            $chat = Chat::where('wam_id', $response['id'])->where('organization_id', $organization->id)->first();

                            if(!$chat){
                                //First open the chat
                                (new ChatService($organization->id))->handleTicketAssignment($contact->id);

                                $chat = new Chat;
                                $chat->organization_id = $organization->id;
                                $chat->wam_id = $response['id'];
                                $chat->contact_id = $contact->id;
                                $chat->type = 'inbound';
                                $chat->metadata = json_encode($response);
                                $chat->status = 'delivered';
                                $chat->save();
                            
                                if($chat){
                                    if($response['type'] === 'image' || $response['type'] === 'video' || $response['type'] === 'audio' || $response['type'] === 'document' || $response['type'] === 'sticker'){
                                        $type = $response['type'];
                                        $mediaId = $response[$type]['id'];

                                        //Get & Download media file
                                        $media = $this->getMedia($mediaId, $organization);
                                        $downloadedFile = $this->downloadMedia($media, $organization);

                                        //Upload media
                                        $chatMedia = new ChatMedia;
                                        $chatMedia->name = $type === 'document' ? $response[$type]['filename'] : 'N/A';
                                        $chatMedia->path = $downloadedFile['media_url'];
                                        $chatMedia->type = $media['mime_type'];
                                        $chatMedia->size = $media['file_size'];
                                        $chatMedia->location = $downloadedFile['location'];
                                        $chatMedia->created_at = now();
                                        $chatMedia->save();

                                        //Update chat
                                        Chat::where('id', $chat->id)->update([
                                            'media_id' => $chatMedia->id
                                        ]);
                                    }
                                }

                                $chat = Chat::with('contact','media')->where('id', $chat->id)->first();

                                $chatlogId = ChatLog::insertGetId([
                                    'contact_id' => $contact->id,
                                    'entity_type' => 'chat',
                                    'entity_id' => $chat->id,
                                    'created_at' => now()
                                ]);
                                
                                $chatLogArray = ChatLog::where('id', $chatlogId)->where('deleted_at', null)->first();
                                $chatArray = array([
                                    'type' => 'chat',
                                    'value' => $chatLogArray->relatedEntities
                                ]);

                                event(new NewChatEvent($chatArray, $organization->id));

                                $isMessageLimitReached = SubscriptionService::isSubscriptionFeatureLimitReached($organization->id, 'message_limit');

                                if(!$isMessageLimitReached){
                                    if($response['type'] === 'text' || $response['type'] === 'button'|| $response['type'] === 'audio'|| $response['type'] === 'interactive'){
                                        (new AutoReplyService)->checkAutoReply($chat, $isNewContact);
                                    }
                                }
                            }
                        }
                    }

                    // Trigger webhook
                    WebhookHelper::triggerWebhookEvent('message.received', [
                        'data' => $res,
                    ], $organization->id);
                }
            }
        } else if($res['field'] === 'message_template_status_update'){
            $response = $res['value'] ?? null;
            $template = Template::where('meta_id', $response['message_template_id'])->first();

            if($template){
                $template->status = $response['event'];
                $template->save();
            }
        } else if($res['field'] === 'account_review_update'){
            //Account Status
            $response = $res['value'] ?? null;
            $organizationConfig = Organization::where('id', $organization->id)->first();
            $metadataArray = $organizationConfig->metadata ? json_decode($organizationConfig->metadata, true) : [];

            $metadataArray['whatsapp']['account_review_status'] = $response['decision'] ?? NULL;

            $updatedMetadataJson = json_encode($metadataArray);
            $organizationConfig->metadata = $updatedMetadataJson;
            $organizationConfig->save();
        } else if($res['field'] === 'phone_number_name_update'){
            //Display Name
            $response = $res['value'] ?? null;

            if($response['decision'] == 'APPROVED'){
                $organizationConfig = Organization::where('id', $organization->id)->first();
                $metadataArray = $organizationConfig->metadata ? json_decode($organizationConfig->metadata, true) : [];

                $metadataArray['whatsapp']['verified_name'] = $response['requested_verified_name'] ?? NULL;

                $updatedMetadataJson = json_encode($metadataArray);
                $organizationConfig->metadata = $updatedMetadataJson;
                $organizationConfig->save();
            }
        } else if($res['field'] === 'phone_number_quality_update'){
            //messaging_tier_limit
            $response = $res['value'] ?? null;
            $organizationConfig = Organization::where('id', $organization->id)->first();
            $metadataArray = $organizationConfig->metadata ? json_decode($organizationConfig->metadata, true) : [];

            $metadataArray['whatsapp']['messaging_limit_tier'] = $response['current_limit'] ?? NULL;

            $updatedMetadataJson = json_encode($metadataArray);
            $organizationConfig->metadata = $updatedMetadataJson;
            $organizationConfig->save();
        } else if($res['field'] === 'business_capability_update'){
            //messaging_tier_limit
            $response = $res['value'] ?? null;
            $organizationConfig = Organization::where('id', $organization->id)->first();
            $metadataArray = $organizationConfig->metadata ? json_decode($organizationConfig->metadata, true) : [];

            $metadataArray['whatsapp']['max_daily_conversation_per_phone'] = $response['max_daily_conversation_per_phone'] ?? NULL;
            $metadataArray['whatsapp']['max_phone_numbers_per_business'] = $response['max_phone_numbers_per_business'] ?? NULL;

            $updatedMetadataJson = json_encode($metadataArray);
            $organizationConfig->metadata = $updatedMetadataJson;
            $organizationConfig->save();
        }

        return Response::json(['status' => 'success'], 200);
    }

    private function downloadMedia($mediaInfo, Organization $organization)
    {
        $metadata = json_decode($organization->metadata);

        if (empty($metadata) || empty($metadata->whatsapp->access_token)) {
            return $this->forbiddenResponse();
        }

        try {
            $client = new Client();

            $requestOptions = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $metadata->whatsapp->access_token,
                    'Content-Type' => 'application/json',
                ],
            ];

            $response = $client->request('GET', $mediaInfo['url'], $requestOptions);

            $fileContent = $response->getBody();
            $mimeType = $mediaInfo['mime_type'] ?? 'application/octet-stream'; // Default fallback
            $fileName = $this->generateFilename($fileContent, $mediaInfo['mime_type']);

            $storage = Setting::where('key', 'storage_system')->first()->value;

            if($storage === 'local'){
                $location = 'local';
                $file = Storage::disk('local')->put('public/' . $fileName, $fileContent);
                $mediaFilePath = $file;
                $mediaUrl = rtrim(config('app.url'), '/') . '/media/' . 'public/' . $fileName;
            } else if($storage === 'aws') {
                $location = 'amazon';
                $filePath = 'uploads/media/received/'  . $organization->id . '/' . Str::random(40) . time();
                $file = Storage::disk('s3')->put($filePath, $fileContent, [
                    'ContentType' => $mimeType
                ]);
                $mediaUrl = Storage::disk('s3')->url($filePath);
            }

            $mediaData = [
                'media_url' => $mediaUrl,
                'location' => $location,
            ];
    
            return $mediaData;
        } catch (RequestException $e) {
            Log::error("Error processing webhook: " . $e->getMessage());
            return Response::json(['error' => 'Failed to download file'], 403);
        }
    }

    private function generateFilename($fileContent, $mimeType)
    {
        // Generate a unique filename based on the file content
        $hash = sha1($fileContent);

        // Get the file extension from the media type
        $extension = explode('/', $mimeType)[1];

        // Combine the hash, timestamp, and extension to create a unique filename
        $filename = "{$hash}_" . time() . ".{$extension}";

        return $filename;
    }

    private function getMedia($mediaId, Organization $organization)
    {
        $metadata = json_decode($organization->metadata);

        if (empty($metadata) || empty($metadata->whatsapp->access_token)) {
            return $this->forbiddenResponse();
        }

        $client = new Client();
        $responseObject = new \stdClass();

        try {
            $requestOptions = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $metadata->whatsapp->access_token,
                    'Content-Type' => 'application/json',
                ],
            ];

            $response = $client->request('GET', "https://graph.facebook.com/v18.0/{$mediaId}", $requestOptions);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            return Response::json(['error' => 'Method Invalid'], 400);
        }
    }

    public function processWebhook(Request $request, $processor)
    {
        $paymentPlatform = $this->paymentPlatformResolver->resolveService($processor);
        session()->put('paymentPlatform', $processor);
        
        return $paymentPlatform->handleWebhook($request);
    }

    /**
     * Handle failed campaign chat - move contact to failed group if all retries exhausted
     */
    protected function handleFailedCampaignChat(Chat $chat, Organization $organization)
    {
        try {
            Log::info("handleFailedCampaignChat called", ['chat_id' => $chat->id]);

            // Find the campaign log linked to this chat (directly or via retry)
            $campaignLog = CampaignLog::with(['campaign', 'retries'])
                ->where('chat_id', $chat->id)
                ->first();

            $isRetryChat = false;
            if (!$campaignLog) {
                // Check if this is a retry chat
                $campaignLog = CampaignLog::with(['campaign', 'retries'])
                    ->whereHas('retries', function ($query) use ($chat) {
                        $query->where('chat_id', $chat->id);
                    })
                    ->first();
                $isRetryChat = true;
            }

            if (!$campaignLog || !$campaignLog->campaign) {
                Log::info("Not a campaign message, skipping", ['chat_id' => $chat->id]);
                return; // Not a campaign message
            }

            Log::info("Found campaign log for failed chat", [
                'chat_id' => $chat->id,
                'campaign_log_id' => $campaignLog->id,
                'is_retry_chat' => $isRetryChat
            ]);

            // Get organization campaign settings
            $orgMetadata = json_decode($organization->metadata ?? '{}', true);
            $campaignSettings = $orgMetadata['campaigns'] ?? [];
            $retryEnabled = $campaignSettings['enable_resend'] ?? false;
            $retryIntervals = $campaignSettings['resend_intervals'] ?? [];
            $maxRetries = count($retryIntervals);
            $moveToGroup = $campaignSettings['move_failed_contacts_to_group'] ?? false;
            $failedGroupUuid = $campaignSettings['failed_campaign_group'] ?? null;

            Log::info("Campaign settings for failed group", [
                'retry_enabled' => $retryEnabled,
                'max_retries' => $maxRetries,
                'move_to_group' => $moveToGroup,
                'failed_group_uuid' => $failedGroupUuid
            ]);

            if (!$moveToGroup || !$failedGroupUuid) {
                Log::info("Move to failed group not enabled, skipping");
                return; // Feature not enabled
            }

            // Check if all retries are exhausted
            $retryCount = $campaignLog->retries->count();
            
            // Get the latest retry to check if this is the final failed attempt
            $latestRetry = $campaignLog->retries()->latest()->first();
            $isLatestRetryChat = $latestRetry && $latestRetry->chat_id == $chat->id;
            $isOriginalChat = $campaignLog->chat_id == $chat->id;

            Log::info("Retry status check", [
                'campaign_log_id' => $campaignLog->id,
                'retry_count' => $retryCount,
                'max_retries' => $maxRetries,
                'is_original_chat' => $isOriginalChat,
                'is_latest_retry_chat' => $isLatestRetryChat,
                'latest_retry_chat_id' => $latestRetry?->chat_id
            ]);

            // Determine if we should move to group:
            // 1. If retry is disabled and original chat failed, move immediately
            // 2. If retry is enabled and max retries reached and latest retry chat failed
            $shouldMoveToGroup = false;
            
            if (!$retryEnabled && $isOriginalChat) {
                // Retry disabled, original message failed
                $shouldMoveToGroup = true;
                Log::info("Retry disabled, original chat failed - will move to group");
            } elseif ($retryEnabled && $retryCount >= $maxRetries && $isLatestRetryChat) {
                // All retries exhausted and this is the final retry that failed
                $shouldMoveToGroup = true;
                Log::info("All retries exhausted, last retry failed - will move to group");
            } elseif ($retryEnabled && $retryCount >= $maxRetries && $isOriginalChat && !$latestRetry) {
                // Edge case: max retries but no retry records (shouldn't happen)
                $shouldMoveToGroup = true;
                Log::info("Max retries reached with no retry records - will move to group");
            }

            if ($shouldMoveToGroup) {
                Log::info("Moving contact to failed group after chat webhook failure", [
                    'campaign_log_id' => $campaignLog->id,
                    'contact_id' => $campaignLog->contact_id,
                    'retry_count' => $retryCount,
                    'max_retries' => $maxRetries,
                    'failed_group_uuid' => $failedGroupUuid
                ]);

                // Get the failed group ID
                $failedGroupId = DB::table('contact_groups')
                    ->where('uuid', $failedGroupUuid)
                    ->value('id');

                if (!$failedGroupId) {
                    Log::warning('Failed to move contact: Group with UUID ' . $failedGroupUuid . ' does not exist.');
                    return;
                }

                // Check if contact is already in this group
                $existsInGroup = DB::table('contact_contact_group')
                    ->where('contact_id', $campaignLog->contact_id)
                    ->where('contact_group_id', $failedGroupId)
                    ->exists();

                if (!$existsInGroup) {
                    // Add contact to the failed group (don't remove from other groups)
                    DB::table('contact_contact_group')->insert([
                        'contact_id' => $campaignLog->contact_id,
                        'contact_group_id' => $failedGroupId,
                    ]);

                    Log::info("Contact moved to failed group", [
                        'contact_id' => $campaignLog->contact_id,
                        'group_id' => $failedGroupId
                    ]);
                } else {
                    Log::info("Contact already in failed group", ['contact_id' => $campaignLog->contact_id]);
                }
            } else {
                Log::info("Not moving to failed group yet - retries still pending or not latest", [
                    'retry_count' => $retryCount,
                    'max_retries' => $maxRetries
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Error handling failed campaign chat: " . $e->getMessage(), [
                'chat_id' => $chat->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}