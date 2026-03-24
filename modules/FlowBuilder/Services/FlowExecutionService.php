<?php

namespace Modules\FlowBuilder\Services;

use App\Helpers\CustomHelper;
use App\Models\Contact;
use App\Models\Organization;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Log;
use Modules\FlowBuilder\Models\Flow;
use Modules\FlowBuilder\Models\FlowLog;
use Modules\FlowBuilder\Models\FlowMedia;
use Modules\FlowBuilder\Models\FlowUserData;
use Modules\FlowBuilder\Services\ActionExecutionService;

class FlowExecutionService
{
    private $whatsappService;
    private $organizationId;

    public function __construct($organizationId)
    {
        $this->organizationId = $organizationId;
        $this->initializeWhatsappService();
    }

    private function initializeWhatsappService()
    {
        Log::info("Initializing WhatsApp service for organization: {$this->organizationId}");
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
    
    /**
     * Execute a flow for a user based on their input.
     *
     * @param $chat
     * @param boolean $isNewContact
     * @param string $message
     * @return FlowStep|null
     */
    public function executeFlow($chat, $isNewContact, $message)
    {
        Log::info("Starting flow execution for contact: {$chat->contact_id}, isNewContact: {$isNewContact}, message: {$message}");
        if(CustomHelper::isModuleEnabled('Flow builder', $chat->organization_id)){
            // Find the current step for the user in the flow
            Log::info("Looking up existing flow data for contact: {$chat->contact_id}");
            $flowData = FlowUserData::where('contact_id', $chat->contact_id)->first();
            $flowId = null;

            if($flowData && $flowData->exists){
                Log::info("Existing flow data found for contact {$chat->contact_id}, flow_id: {$flowData->flow_id}");
                // Check if the flow still exists in the database
                $flow = Flow::find($flowData->flow_id);
                
                if(!$flow){
                    // Flow has been deleted, remove FlowUserData and proceed as if it doesn't exist
                    Log::warning("DELETION POINT 1: Flow not found, deleting FlowUserData for contact {$chat->contact_id}");
                    FlowUserData::where('contact_id', $chat->contact_id)->delete();
                    $flowData = null;
                } else {
                    // Flow exists, proceed to process flow
                    Log::info("Flow exists for contact {$chat->contact_id}, processing flow with id: {$flowData->flow_id}");
                    $flowId = $flowData->flow_id;
                    $result = $this->processFlow($chat, $isNewContact, $flowData, $chat->contact_id, strtolower(trim($message)));
                    
                    // If validation failed, don't delete flow data
                    if ($result === 'validation_failed') {
                        Log::info("Validation failed for contact {$chat->contact_id}, keeping flow data");
                        return false; // Return false but don't delete flow data
                    }
                    
                    // If flow is delayed, don't delete flow data
                    if ($result === 'delayed') {
                        Log::info("Flow execution delayed for contact {$chat->contact_id}, keeping flow data");
                        return false; // Return false but don't delete flow data
                    }
                    
                    return $result;
                }
            }

            // If flowData doesn't exist or was deleted, proceed with flow determination logic
            if(!$flowData){
                Log::info("No existing flow data for contact {$chat->contact_id}, determining flow based on trigger");
                // Determine the flow based on trigger type
                $flowQuery = Flow::where('organization_id', $chat->organization_id)->where('status', 'active');
                $flow = null;

                //Check if any flow trigger has been hit
                if($isNewContact){
                    Log::info("Checking for new_contact trigger flow for contact {$chat->contact_id}");
                    $flow = $flowQuery->where('trigger', 'new_contact')->first();
                } else {
                    Log::info("Checking for keyword trigger flow for contact {$chat->contact_id}, message: {$message}");
                    $msg = strtolower(trim($message)); // Normalize the message
                    
                    // PRIORITY 1: First try to match the EXACT full message/phrase
                    // This ensures "NON INTERESTED" matches flow with keyword "NON INTERESTED" 
                    // before matching flow with keyword "Interested"
                    $flow = \DB::table('flows')->whereRaw(
                        '`trigger` = ? AND organization_id = ? AND status = ? AND FIND_IN_SET(?, LOWER(REPLACE(keywords, \', \', \',\')))',
                        ['keywords', $chat->organization_id, 'active', $msg]
                    )->first();
                    
                    // PRIORITY 2: If no exact match, try individual word matches
                    if (!$flow) {
                        $words = explode(' ', $msg); // Split message into individual words
                        $conditions = [];
                        $bindings = [];

                        // Add individual word checks
                        foreach ($words as $word) {
                            $word = strtolower(trim($word));
                            if (!empty($word)) {
                                $conditions[] = "FIND_IN_SET(?, LOWER(REPLACE(keywords, ', ', ',')))";
                                $bindings[] = $word;
                            }
                        }

                        if (!empty($conditions)) {
                            // Only try to find a flow for the specific keyword(s)
                            $flow = \DB::table('flows')->whereRaw(
                                '( `trigger` = ? AND organization_id = ? AND status = ?) AND (' . implode(' OR ', $conditions) . ')',
                                array_merge(['keywords', $chat->organization_id, 'active'], $bindings)
                            )->first();
                        }
                    }
                }

                // Set the flow ID if a matching flow is found
                if ($flow) {
                    Log::info("Matching flow found for contact {$chat->contact_id}, flow_id: {$flow->id}");
                    $flowId = $flow->id;
                } else {
                    Log::info("No matching flow found for contact {$chat->contact_id}");
                }

                // If a flow ID was found, create a new FlowUserData record
                if ($flowId) {
                    Log::info("Creating new FlowUserData for contact {$chat->contact_id}, flow_id: {$flowId}");
                    $flowData = new FlowUserData();
                    $flowData->fill([
                        'contact_id' => $chat->contact_id,
                        'flow_id' => $flowId,
                        'current_step' => 1
                    ])->save();
                    
                    $result = $this->processFlow($chat, $isNewContact, $flowData, $chat->contact_id, strtolower(trim($message)));
                    
                    // If validation failed, don't delete flow data
                    if ($result === 'validation_failed') {
                        return false; // Return false but don't delete flow data
                    }
                    
                    // If flow is delayed, don't delete flow data
                    if ($result === 'delayed') {
                        return false; // Return false but don't delete flow data
                    }
                    
                    return $result;
                }
            }

            return false;
        }
    }

    public function hasActiveFlow($chat){
        Log::info("Checking for active flow for contact: {$chat->contact_id}");
        $activeFlow = FlowUserData::where('contact_id', $chat->contact_id)->first();
        Log::info("Active flow result for contact {$chat->contact_id}: " . ($activeFlow ? 'true' : 'false'));
        return $activeFlow ? true : false;
    }

    /**
     * Continue a flow that was paused due to a delay action
     *
     * @param int $contactId
     * @param int $flowId
     * @param int $currentStep
     * @return bool
     */
    public function continueDelayedFlow($contactId, $flowId, $currentStep)
    {
        Log::info("Continuing delayed flow for contact: {$contactId}, flow_id: {$flowId}, current_step: {$currentStep}");
        // Find the flow user data
        $flowData = FlowUserData::where('contact_id', $contactId)
            ->where('flow_id', $flowId)
            ->first();

        if (!$flowData) {
            Log::warning("FlowUserData not found for delayed flow continuation: contact {$contactId}, flow {$flowId}");
            return false;
        }

        // Update the current step if needed
        if ($flowData->current_step != $currentStep) {
            Log::info("Updating current_step for contact {$contactId} from {$flowData->current_step} to {$currentStep}");
            $flowData->current_step = $currentStep;
            $flowData->save();
        }

        // CRITICAL FIX: Advance to the next step BEFORE continuing the flow
        // This prevents re-executing the delay action that we just completed
        $flow = Flow::find($flowData->flow_id);
        if ($flow && !empty($flow->metadata)) {
            $edgesArray = json_decode($flow->metadata, true);
            $edges = \Arr::get($edgesArray, "edges", null);
            
            // Find the next step from the current delay action node
            $nextNodes = [];
            foreach ($edges as $edge) {
                if (isset($edge['source']) && (string) $edge['source'] === (string) $flowData->current_step) {
                    if (isset($edge['targetNode']['id'])) {
                        $nextNodes[] = $edge['targetNode']['id'];
                    }
                }
            }
            
            if (!empty($nextNodes)) {
                $nextStep = $nextNodes[0];
                Log::info("Moving to next step after delay: {$nextStep} for contact {$contactId}");
                $flowData->current_step = $nextStep;
                $flowData->save();
            }
        }

        // Get the contact
        $contact = Contact::find($contactId);
        if (!$contact) {
            Log::warning("Contact not found for delayed flow continuation: {$contactId}");
            return false;
        }

        // Create a mock chat object for processing
        $chat = (object) [
            'contact_id' => $contactId,
            'organization_id' => $this->organizationId
        ];

        // Continue processing the flow from the NEXT step (not the delay action)
        return $this->processFlow($chat, false, $flowData, $contactId, '');
    }

    public function processFlow($chat, $isNewContact, $flowData, $contactId, $message){
        Log::info("Processing flow for contact: {$contactId}, flow_id: {$flowData->flow_id}, current_step: {$flowData->current_step}");
        $flow = Flow::find($flowData->flow_id);

        if (!$flow || empty($flow->metadata)) {
            Log::warning("Flow not found or metadata empty for flow_id: {$flowData->flow_id}");
            return;
        }

        $contact = Contact::find($contactId);
        if (!$contact) {
            Log::warning("Contact not found: {$contactId}");
            return false;
        }

        $edgesArray = json_decode($flow->metadata, true);
        $edges = \Arr::get($edgesArray, "edges", null);
        
        // Process nodes continuously until we encounter a message node
        $maxIterations = 50; // Prevent infinite loops
        $iteration = 0;
        
        while ($iteration < $maxIterations) {
            $iteration++;
            Log::info("Flow iteration {$iteration} for contact {$contactId}, current_step: {$flowData->current_step}");
            
            // Get the current node metadata
            $metadataArray = $this->findEdgesBySource($edges, $flowData->current_step, $message);

            if(empty($metadataArray)){
                Log::warning("DELETION POINT 2: No next step found for contact {$contactId}, ending flow");
                FlowUserData::where('contact_id', $contactId)->delete();
                return false;
            }

            // Check if this is an action node
            $nodeType = \Arr::get($metadataArray, "type", null);
            Log::info("Processing node of type: {$nodeType} for contact {$contactId}");
            if ($nodeType === 'action') {
                $result = $this->processActionNode($metadataArray, $contact, $message, $flowData, $contactId);
                
                if ($result === false) {
                    // Action failed, stop the flow
                    Log::warning("Action node failed for contact {$contactId}");
                    return false;
                }
                
                if ($result === 'validation_failed') {
                    // Validation failed (e.g., invalid email), stay on the same node
                    Log::info("Action validation failed for contact {$contactId}, staying on same node");
                    return 'validation_failed'; // Return special value to indicate validation failure
                }
                
                if ($result === 'delayed') {
                    // Flow is paused due to delay action, don't proceed to next step
                    Log::info("Action delayed flow for contact {$contactId}");
                    return 'delayed'; // Return special value to indicate the flow is paused
                }
                
                // Action completed successfully, refresh flowData and continue to next node
                Log::info("Action completed successfully for contact {$contactId}, refreshing flow data");
                $flowData = FlowUserData::where('contact_id', $contactId)->first();
                if (!$flowData) {
                    Log::warning("DELETION POINT 7: FlowUserData not found after action, ending flow");
                    return false;
                }
                continue;
            }

            // This is a message node (text, media, interactive, etc.) - process it
            $messageResult = $this->processMessageNode($metadataArray, $contact, $flowData, $contactId);
            
            if (!$messageResult) {
                return false;
            }

            // After sending a message, check if the NEXT node is an action node.
            // If so, continue the loop to auto-execute it (e.g., delay, update_contact).
            // If next node is a message node or doesn't exist, stop and wait for user reply.
            $flowData = FlowUserData::where('contact_id', $contactId)->first();
            if (!$flowData) {
                return true;
            }

            $nextMetadata = $this->findEdgesBySource($edges, $flowData->current_step, '');
            $nextNodeType = \Arr::get($nextMetadata, "type", null);

            if ($nextNodeType === 'action') {
                // Check if the next action has wait_for_reply enabled
                $nextConfig = \Arr::get($nextMetadata, "data.config", []);
                $nextWaitForReply = $nextConfig['wait_for_reply'] ?? false;
                
                if ($nextWaitForReply) {
                    // Action needs user's reply as input — stop and wait for user to reply
                    Log::info("Next action node has wait_for_reply enabled, waiting for user reply before executing for contact {$contactId}");
                    return true;
                }
                
                // Next node is an action without wait_for_reply — continue the loop to auto-execute it
                Log::info("Next node after message is action type, auto-continuing for contact {$contactId}");
                continue;
            }

            // Next node is a message node or end of flow — stop and wait for user reply
            return true;
        }
        
        Log::warning("DELETION POINT 3: Maximum iterations reached for contact {$contactId}, ending flow");
        FlowUserData::where('contact_id', $contactId)->delete();
        return false;
    }

    /**
     * Process message nodes (text, media, interactive buttons, etc.)
     */
    private function processMessageNode($metadataArray, $contact, $flowData, $contactId)
    {
        Log::info("Processing message node for contact: {$contactId}");
        $fieldsArray = \Arr::get($metadataArray, "data.metadata.fields", null);
        $type = $fieldsArray['type'] ?? null;
        Log::info("Message node type: {$type} for contact {$contactId}");

        $message = $this->replacePlaceholders($contact->uuid, $fieldsArray['body'] ?? '');
        // Decode any HTML entities (e.g. &amp; → &) for WhatsApp plain text
        $message = html_entity_decode($message, ENT_QUOTES, 'UTF-8');

        // Initialize the header array if needed for interactive messages
        $header = $this->prepareHeader($fieldsArray ?? []);
        $buttonArray = [];
        $buttonType = null;
        $buttonLabel = null;

        if($type == 'text'){
            $buttonType = 'text';
        } elseif ($type === 'interactive buttons') {
            $buttonType = ($fieldsArray['buttonType'] ?? '') === 'buttons'
                ? 'interactive buttons'
                : 'interactive call to action url';
            $buttonArray = $this->prepareButtonArray($fieldsArray ?? [], $buttonType);
    
        } elseif ($type === 'interactive list') {
            $buttonType = 'interactive list';
            $buttonArray = $this->prepareButtonArray($fieldsArray ?? [], $buttonType);
            $buttonLabel = $fieldsArray['buttonLabel'] ?? '';
        }

        $response = null;
        Log::info("Preparing to send message of type '{$type}' to contact: {$contactId}");

        switch ($type) {
            case 'text':
                $response = $this->whatsappService->sendMessage($contact->uuid, $message, 0, $buttonType);
                break;
    
            case 'media':
                $mediaInfo = json_decode(($fieldsArray['media']['metadata'] ?? '{}'), true);
                $mediaLocation = $fieldsArray['media']['location'] ?? '';
                $mediaLocation = ($mediaLocation === 'aws') ? 'amazon' : $mediaLocation;

                $response = $this->whatsappService->sendMedia(
                    $contact->uuid,
                    $fieldsArray['mediaType'] ?? '',
                    $mediaInfo['name'] ?? '',
                    $fieldsArray['media']['path'] ?? '',
                    $fieldsArray['media']['path'] ?? '',
                    $mediaLocation,
                    $fieldsArray['caption'] ?? ''
                );
                break;
    
            case 'interactive buttons':
                $response = $this->whatsappService->sendMessage(
                    $contact->uuid,
                    $message,
                    0,
                    $buttonType,
                    $buttonArray,
                    $header,
                    ($fieldsArray['footer'] ?? '')
                );
                break;

            case 'interactive list':
                $response = $this->whatsappService->sendMessage(
                    $contact->uuid,
                    $message,
                    0,
                    $buttonType,
                    $buttonArray,
                    $header,
                    ($fieldsArray['footer'] ?? ''),
                    $buttonLabel
                );
                break;
        }

        if($response){
            // Log::info("Response: " . json_encode($response));
            Log::info("Message sent successfully for contact {$contactId}, proceeding to next step");
            // Update to the next step
            $this->proceedToNextStep($flowData, $contactId);
            
            if(isset($response->data->chat->id)){
                FlowLog::create([
                    'flow_id' => $flowData->flow_id,
                    'chat_id' => $response->data->chat->id
                ]);
            }
            
            return true;
        } else {
            // Even if message sending fails, we should still proceed to next step to avoid getting stuck
            Log::warning("Message sending failed for contact {$contactId}, but proceeding to next step to avoid getting stuck");
            $this->proceedToNextStep($flowData, $contactId);
            return true;
        }
    }

    /**
     * Process action nodes in the flow
     */
    private function processActionNode($metadataArray, $contact, $message, $flowData, $contactId)
    {
        Log::info("Processing action node for contact: {$contactId}");
        $actionType = \Arr::get($metadataArray, "data.actionType", null);
        $config = \Arr::get($metadataArray, "data.config", []);
        $isActive = \Arr::get($metadataArray, "data.is_active", true);
        Log::info("Action type: {$actionType}, is_active: {$isActive} for contact {$contactId}");

        if (!$actionType || !$isActive) {
            // If action is not active or has no type, just proceed to next step
            Log::info("Action not active or has no type, proceeding to next step for contact {$contactId}");
            $this->proceedToNextStep($flowData, $contactId);
            return true;
        }

        // Normalize action type: convert hyphens to underscores
        $actionType = str_replace('-', '_', $actionType);
        Log::info("Normalized action type: {$actionType}");

        // Initialize action execution service
        Log::info("Initializing action execution service for action: {$actionType}");
        $actionService = new ActionExecutionService($this->organizationId);

        // Execute the action
        Log::info("Executing action: {$actionType} for contact {$contactId}");
        $result = $actionService->executeAction($actionType, $config, $contact, $message, $flowData, $contactId);

        // Handle conditional actions specially
        if ($actionType === 'conditional') {
            Log::info("Handling conditional action for contact: {$contactId}, condition result: {$result}");
            // Set current_step to this conditional node's ID before processing
            $conditionalNodeId = $metadataArray['id'] ?? null;
            if ($conditionalNodeId && $flowData->current_step != $conditionalNodeId) {
                Log::info("Updating conditional node ID from {$flowData->current_step} to {$conditionalNodeId}");
                FlowUserData::where('contact_id', $contactId)->update(['current_step' => $conditionalNodeId]);
                $flowData = FlowUserData::where('contact_id', $contactId)->first();
            }
            return $this->handleConditionalAction($result, $flowData, $contactId, $metadataArray);
        }

        // For update_contact action, check if it failed due to validation BEFORE checking general failure
        if ($actionType === 'update_contact' && $result === false) {
            // Check if there's an invalid email message configured, which indicates validation failure
            $invalidMessage = $config['invalid_email_message'] ?? '';
            Log::info("Update contact validation failed, staying on same node for contact {$contactId}");
            return 'validation_failed'; // Special return value to indicate validation failure
        }

        // Check if action returned 'delayed' status
        if ($result === 'delayed') {
            Log::info("Delay action scheduled for contact {$contactId}, pausing flow");
            return 'delayed'; // Return special value to indicate the flow is paused
        }

        if ($result === false) {
            // Action failed, stop the flow
            Log::warning("DELETION POINT 4: Action {$actionType} failed, deleting FlowUserData for contact {$contactId}");
            FlowUserData::where('contact_id', $contactId)->delete();
            return false;
        }

        // For other actions, proceed to next step
        Log::info("Action {$actionType} completed successfully for contact {$contactId}");
        $this->proceedToNextStep($flowData, $contactId);
        return true;
    }

    /**
     * Handle conditional action branching
     */
    private function handleConditionalAction($conditionResult, $flowData, $contactId, $metadataArray)
    {
        Log::info("Handling conditional action with result: {$conditionResult} for contact: {$contactId}");
        $flow = Flow::find($flowData->flow_id);
        if (!$flow || empty($flow->metadata)) {
            Log::warning("Flow not found or empty metadata for conditional action, contact: {$contactId}");
            return false;
        }

        $edgesArray = json_decode($flow->metadata, true);
        $edges = \Arr::get($edgesArray, "edges", null);
        $currentStep = $flowData->current_step;

        // Build the sourceHandle for the condition or default
        if ($conditionResult !== 'default') {
            $sourceHandle = 'condition-' . $conditionResult . '|' . $currentStep;
        } else {
            $sourceHandle = 'default|' . $currentStep;
        }

        Log::info("Looking for edge with source: {$currentStep} and sourceHandle: {$sourceHandle}");

        foreach ($edges as $edge) {
            if (
                isset($edge['source']) && (string)$edge['source'] === (string)$currentStep &&
                isset($edge['sourceHandle']) && $edge['sourceHandle'] === $sourceHandle
            ) {
                $targetNode = $edge['targetNode']['id'] ?? null;
                if ($targetNode) {
                    Log::info("Found matching edge, routing to node: " . $targetNode . " for contact: {$contactId}");
                    // Reload flowData
                    Log::info("Reloading flowData for contact: {$contactId}");
                    $flowData = FlowUserData::where('contact_id', $contactId)->first();
                    // Get the chat object (you may need to pass it in, or reconstruct it)
                    $chat = (object) [
                        'contact_id' => $contactId,
                        'organization_id' => $this->organizationId
                    ];
                    // Process the next node
                    $this->processFlow($chat, false, $flowData, $contactId, '');
                    return true;
                }
            }
        }

        // No matching path found, end the flow
        \Log::warning("DELETION POINT 5: No matching conditional path found, deleting FlowUserData for contact {$contactId}");
        FlowUserData::where('contact_id', $contactId)->delete();
        return false;
    }

    /**
     * Proceed to the next step in the flow
     */
    private function proceedToNextStep($flowData, $contactId)
    {
        Log::info("Proceeding to next step for contact: {$contactId}, current_step: {$flowData->current_step}");
        // Find the next node by looking at the edges
        $flow = Flow::find($flowData->flow_id);
        if (!$flow || empty($flow->metadata)) {
            Log::warning("DELETION POINT 8: Flow not found or empty metadata for flow {$flowData->flow_id}");
            return;
        }

        $edgesArray = json_decode($flow->metadata, true);
        $edges = \Arr::get($edgesArray, "edges", null);
        
        // Find edges that start from the current step
        $nextNodes = [];
        foreach ($edges as $edge) {
            if (isset($edge['source']) && (string) $edge['source'] === (string) $flowData->current_step) {
                if (isset($edge['targetNode']['id'])) {
                    $nextNodes[] = $edge['targetNode']['id'];
                }
            }
        }
        
        if (!empty($nextNodes)) {
            // Use the first next node found
            $nextStep = $nextNodes[0];
            Log::info("Found next step: {$nextStep} for contact {$contactId}, updating current_step");
            FlowUserData::where('contact_id', $contactId)->update(['current_step' => $nextStep]);
        } else {
            Log::warning("DELETION POINT 6: No next step found for current_step {$flowData->current_step}, ending flow");
            FlowUserData::where('contact_id', $contactId)->delete();
        }
    }

    private function prepareHeader(array $fieldsArray): array
    {
        $header = [];

        if (($fieldsArray['headerType'] ?? '') === 'text') {
            $header = [
                'type' => 'text',
                'text' => clean($fieldsArray['headerText'] ?? ''),
            ];
        } elseif (($fieldsArray['headerType'] ?? '') !== 'none') {
            $header['type'] = $fieldsArray['headerType'] ?? '';
            $header[$fieldsArray['headerType'] ?? ''] = [
                'link' => $fieldsArray['headerMedia']['path'] ?? '',
            ];
        }

        return $header;
    }

    private function prepareButtonArray(array $fieldsArray, string $type): array
    {
        $buttonArray = [];

        if ($type === 'interactive buttons') {
            foreach ($fieldsArray['buttons'] ?? [] as $id => $title) {
                if (!empty($title)) {
                    $buttonArray[] = [
                        'id' => $id,
                        'title' => $title,
                    ];
                }
            }
        } elseif ($type === 'interactive call to action url') {
            $buttonArray = [
                'display_text' => $fieldsArray['ctaUrlButton']['displayText'] ?? '',
                'url' => $fieldsArray['ctaUrlButton']['url'] ?? '',
            ];
        } elseif ($type === 'interactive list') {
            $buttonArray = collect($fieldsArray['sections'] ?? [])->map(function ($section) {
                return [
                    'title' => $section['title'] ?? '',
                    'rows' => collect($section['rows'] ?? [])->map(function ($row) {
                        return [
                            'id' => $row['id'] ?? '',
                            'title' => $row['title'] ?? '',
                            'description' => $row['description'] ?? '',
                        ];
                    })->all()
                ];
            })->all();
        }

        return $buttonArray;
    }

    private function findEdgesBySource($edges, $sourceId, $message)
    {
        // Convert $sourceId to a string to handle loose type matching
        $sourceId = (string) $sourceId;
        
        Log::debug("findEdgesBySource: sourceId={$sourceId}, message={$message}, total_edges=" . count($edges));
        
        // Initialize an empty array to store matching edges
        $matchingEdges = [];

        // Iterate over each edge to find matches with sourceId
        foreach ($edges as $index => $edge) {
            // Check if 'source' key exists and matches the source ID
            if (isset($edge['source']) && (string) $edge['source'] === $sourceId) {
                // If there's a match, add this edge to the matching array
                $matchingEdges[] = $edge;
                Log::debug("Found matching edge: source={$edge['source']}, target=" . ($edge['targetNode']['id'] ?? 'unknown'));
            }
        }

        if (count($matchingEdges) === 1) {
            Log::debug("Single edge found, returning targetNode");
            return $matchingEdges[0]['targetNode'] ?? [];
        } else if (count($matchingEdges) > 1) {
            $firstEdge = $matchingEdges[0];
            $nodeType = $firstEdge['sourceNode']['type'] ?? null;
            $type = $firstEdge['sourceNode']['data']['metadata']['fields']['type'] ?? null;

            // Handle action nodes (they don't require user input)
            if ($nodeType === 'action') {
                // For action nodes, just return the first edge (they proceed automatically)
                return $matchingEdges[0]['targetNode'] ?? [];
            }

            // Perform logic based on the 'type'
            if ($type == 'interactive buttons') {
                $buttons = $firstEdge['sourceNode']['data']['metadata']['fields']['buttons'] ?? [];

                // Define button mapping
                $buttonMapping = ['button1' => 'a', 'button2' => 'b', 'button3' => 'c'];
                $handle = null;

                // Check for a match with the message in the buttons array
                foreach ($buttons as $buttonKey => $buttonValue) {
                    if (strtolower(trim($buttonValue)) === $message) {
                        Log::debug("Button match found: {$buttonKey} = {$buttonValue}");
                        $handle = $buttonMapping[$buttonKey] ?? null;
                    }
                }

                
                if($handle != null){
                    Log::debug("Searching for edge with handle: {$handle}");
                    // Search for the edge with this handle and return its targetNode
                    foreach ($matchingEdges as $edge) {
                        if (isset($edge['sourceHandle']) && $edge['sourceHandle'] === $handle) {
                            Log::debug("Found matching handle edge, routing to target node");
                            return $edge['targetNode'] ?? [];
                        }
                    }
                }
            } elseif ($type == 'interactive list') {
                $sections = $firstEdge['sourceNode']['data']['metadata']['fields']['sections'] ?? [];
                $handle = null;

                // Search for the matching title and generate the handle dynamically
                foreach ($sections as $sectionIndex => $section) {
                    if (isset($section['rows']) && is_array($section['rows'])) {
                        foreach ($section['rows'] as $rowIndex => $row) {
                            if (isset($row['title']) && strtolower(trim($row['title'])) === strtolower(trim($message))) {
                                // Construct the handle using section and row positions
                                Log::debug("List item match found at section {$sectionIndex}, row {$rowIndex}");
                                $handle = 'a' . $sectionIndex . $rowIndex;
                            }
                        }
                    }
                }

                if($handle != null){
                    Log::debug("Searching for list edge with handle: {$handle}");
                    foreach ($matchingEdges as $edge) {
                        if (isset($edge['sourceHandle']) && $edge['sourceHandle'] === $handle) {
                            Log::debug("Found matching list edge handle, routing to target node");
                            return $edge['targetNode'] ?? [];
                        }
                    }
                }
            }

            return [];
        }

        return [];
    }

    private function replacePlaceholders($contactUuid, $message){
        Log::info("Replacing placeholders in message: {$message} for contact UUID: {$contactUuid}");
        $organization = Organization::where('id', $this->organizationId)->first();
        $contact = Contact::with('contactGroups')->where('uuid', $contactUuid)->first();
        $address = $contact->address ? json_decode($contact->address, true) : [];
        $metadata = $contact->metadata ? json_decode($contact->metadata, true) : [];
        $full_address = ($address['street'] ?? Null) . ', ' .
                        ($address['city'] ?? Null) . ', ' .
                        ($address['state'] ?? Null) . ', ' .
                        ($address['zip'] ?? Null) . ', ' .
                        ($address['country'] ?? Null);

        $data = [
            'first_name' => $contact->first_name ?? Null,
            'last_name' => $contact->last_name ?? Null,
            'full_name' => $contact->full_name ?? Null,
            'email' => $contact->email ?? Null,
            'phone' => $contact->phone ?? Null,
            'phone_number' => $contact->phone ?? Null,
            'organization_name' => $organization->name,
            'full_address' => $full_address,
            'street' => $address['street'] ?? Null,
            'city' => $address['city'] ?? Null,
            'state' => $address['state'] ?? Null,
            'zip_code' => $address['zip'] ?? Null,
            'country' => $address['country'] ?? Null,
        ];

        $transformedMetadata = [];
        if($metadata){
            foreach ($metadata as $key => $value) {
                $transformedKey = strtolower(str_replace(' ', '_', $key));
                $transformedMetadata[$transformedKey] = $value;
            }
        }

        $mergedData = array_merge($data, $transformedMetadata);

        Log::debug("Placeholder data merged: " . json_encode($mergedData));

        return preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($mergedData) {
            $key = $matches[1];
            return isset($mergedData[$key]) ? $mergedData[$key] : $matches[0];
        }, $message);
    }
}