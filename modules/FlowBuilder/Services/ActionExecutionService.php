<?php

namespace Modules\FlowBuilder\Services;

use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Organization;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Modules\IntelliReply\Services\RagService;

class ActionExecutionService
{
    private $whatsappService;
    private $organizationId;
    private $organization;

    public function __construct($organizationId)
    {
        $this->organizationId = $organizationId;
        $this->organization = Organization::find($organizationId);
        $this->initializeWhatsappService();
    }

    private function initializeWhatsappService()
    {
        $config = $this->organization->metadata;
        $config = $config ? json_decode($config, true) : [];

        $accessToken = $config['whatsapp']['access_token'] ?? null;
        $apiVersion = config('graph.api_version');
        $appId = $config['whatsapp']['app_id'] ?? null;
        $phoneNumberId = $config['whatsapp']['phone_number_id'] ?? null;
        $wabaId = $config['whatsapp']['waba_id'] ?? null;

        $this->whatsappService = new WhatsappService($accessToken, $apiVersion, $appId, $phoneNumberId, $wabaId, $this->organizationId);
    }

    /**
     * Execute an action based on its type and configuration
     *
     * @param string $actionType
     * @param array $config
     * @param Contact $contact
     * @param string $userMessage
     * @param object $flowData
     * @param int $contactId
     * @return bool|string
     */
    public function executeAction($actionType, $config, $contact, $userMessage = '', $flowData = null, $contactId = null)
    {
        try {
            switch ($actionType) {
                case 'add_to_group':
                    return $this->executeAddToGroup($config, $contact);
                
                case 'remove_from_group':
                    return $this->executeRemoveFromGroup($config, $contact);
                
                case 'update_contact':
                    return $this->executeUpdateContact($config, $contact, $userMessage);
                
                case 'send_email':
                    return $this->executeSendEmail($config, $contact);
                
                case 'delay':
                    return $this->executeDelay($config, $contact, $flowData, $contactId);
                
                case 'webhook':
                    return $this->executeWebhook($config, $contact, $userMessage);
                
                case 'ai_response':
                    return $this->executeAIResponse($config, $contact, $userMessage);
                
                case 'conditional':
                    return $this->executeConditional($config, $contact, $userMessage);
                
                default:
                    Log::warning("Unknown action type: {$actionType}");
                    return false;
            }
        } catch (\Exception $e) {
            Log::error("Error executing action {$actionType}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add contact to a group
     */
    private function executeAddToGroup($config, $contact)
    {
        $groupId = $config['group_id'] ?? null;
        if (!$groupId) {
            Log::warning("No group_id specified for add_to_group action");
            return false;
        }

        $group = ContactGroup::where('id', $groupId)
            ->where('organization_id', $this->organizationId)
            ->first();

        if (!$group) {
            Log::warning("Group not found: {$groupId}");
            return false;
        }

        // Check if contact is already in the group
        if (!$contact->contactGroups()->where('contact_group_id', $groupId)->exists()) {
            $contact->contactGroups()->attach($groupId);
            // Log::info("Contact {$contact->id} added to group {$groupId}");
        }

        return true;
    }

    /**
     * Remove contact from a group
     */
    private function executeRemoveFromGroup($config, $contact)
    {
        $groupId = $config['group_id'] ?? null;
        if (!$groupId) {
            Log::warning("No group_id specified for remove_from_group action");
            return false;
        }

        $contact->contactGroups()->detach($groupId);
        // Log::info("Contact {$contact->id} removed from group {$groupId}");
        
        return true;
    }

    /**
     * Update contact field with user's message
     */
    private function executeUpdateContact($config, $contact, $userMessage)
    {
        $targetField = $config['target_field'] ?? null;
        if (!$targetField || !$userMessage) {
            Log::warning("No target_field or userMessage for update_contact action");
            return false;
        }

        // Map field names to actual contact attributes
        $fieldMapping = [
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'email' => 'email',
            'phone' => 'phone',
            'address.street' => 'address->street',
            'address.city' => 'address->city',
            'address.state' => 'address->state',
            'address.zip' => 'address->zip',
            'address.country' => 'address->country'
        ];

        if (isset($fieldMapping[$targetField])) {
            $field = $fieldMapping[$targetField];
            
            // Validate email field
            if ($targetField === 'email' && !empty($userMessage)) {
                if (!filter_var($userMessage, FILTER_VALIDATE_EMAIL)) {
                    Log::warning("Invalid email format provided: {$userMessage}");
                    
                    // Send invalid email message if configured
                    $invalidMessage = $config['invalid_email_message'] ?? 'Please provide a valid email address.';
                    if (!empty($invalidMessage)) {
                        $this->whatsappService->sendMessage($contact->uuid, $invalidMessage);
                    }
                    
                    return false;
                }
            }
            
            if (str_contains($field, '->')) {
                // Handle nested JSON fields like address
                $parts = explode('->', $field);
                $jsonField = $parts[0];
                $jsonKey = $parts[1];
                
                $currentValue = $contact->$jsonField ? json_decode($contact->$jsonField, true) : [];
                $currentValue[$jsonKey] = $userMessage;
                $contact->$jsonField = json_encode($currentValue);
            } else {
                // Handle regular fields
                $contact->$field = $userMessage;
            }
            
            $contact->save();

            // Sync updated contact to sheet
            $this->dispatchSheetSync($contact);

            return true;
        } else {
            // Handle custom metadata fields — strip "metadata." prefix if present
            $metadataKey = preg_replace('/^metadata\./', '', $targetField);
            $metadata = $contact->metadata ? json_decode($contact->metadata, true) : [];
            $metadata[$metadataKey] = $userMessage;
            $contact->metadata = json_encode($metadata);
            $contact->save();
            
            // Sync updated contact to sheet
            $this->dispatchSheetSync($contact);

            return true;
        }
    }

    /**
     * Dispatch sheet sync job for a contact (delayed, deduplicated).
     */
    private function dispatchSheetSync($contact)
    {
        try {
            $cacheKey = "sheet_sync_pending_{$contact->organization_id}_{$contact->id}";

            // If a sync is already pending for this contact, skip (will pick up latest data anyway)
            if (\Illuminate\Support\Facades\Cache::get($cacheKey)) {
                return;
            }

            // Mark sync as pending for 2 minutes
            \Illuminate\Support\Facades\Cache::put($cacheKey, true, 120);

            // Dispatch with 30 second delay so all field updates finish first
            \App\Jobs\SyncContactToSheetJob::dispatch($contact->organization_id, $contact->id)
                ->delay(now()->addSeconds(30));
        } catch (\Exception $e) {
            Log::warning("SheetSync dispatch failed for contact {$contact->id}: " . $e->getMessage());
        }
    }

    /**
     * Send email to contact
     */
    private function executeSendEmail($config, $contact)
    {
        if (!$contact->email) {
            Log::warning("Contact {$contact->id} has no email address");
            return false;
        }

        $subject = $config['subject'] ?? '';
        $body = $config['body'] ?? '';
        $smtpHost = $config['smtp_host'] ?? '';
        $smtpPort = $config['smtp_port'] ?? 587;
        $smtpUsername = $config['smtp_username'] ?? '';
        $smtpPassword = $config['smtp_password'] ?? '';
        $smtpEncryption = $config['smtp_encryption'] ?? 'tls';
        $fromName = $config['from_name'] ?? $this->organization->name;
        $fromEmail = $config['from_email'] ?? '';

        // If no from_email is specified, try to use smtp_username if it's a valid email
        if (empty($fromEmail)) {
            if (filter_var($smtpUsername, FILTER_VALIDATE_EMAIL)) {
                $fromEmail = $smtpUsername;
            } else {
                Log::warning("No valid from_email specified and smtp_username is not a valid email address");
                return false;
            }
        }

        if (!$subject || !$body || !$smtpHost || !$smtpUsername || !$smtpPassword) {
            Log::warning("Incomplete email configuration for send_email action");
            return false;
        }

        // Replace placeholders in subject and body
        $subject = $this->replacePlaceholders($contact, $subject);
        $body = $this->replacePlaceholders($contact, $body);

        try {
            // Configure mail settings
            config([
                'mail.mailers.smtp.host' => $smtpHost,
                'mail.mailers.smtp.port' => $smtpPort,
                'mail.mailers.smtp.username' => $smtpUsername,
                'mail.mailers.smtp.password' => $smtpPassword,
                'mail.mailers.smtp.encryption' => $smtpEncryption,
            ]);

            Mail::raw($body, function ($message) use ($contact, $subject, $fromName, $fromEmail) {
                $message->to($contact->email)
                        ->subject($subject)
                        ->from($fromEmail, $fromName);
            });

            // Log::info("Email sent to {$contact->email}: {$subject}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute delay action
     */
    private function executeDelay($config, $contact, $flowData = null, $contactId = null)
    {
        $duration = $config['duration'] ?? 5; // in minutes
        $unit = $config['unit'] ?? 'minutes';

        Log::info("=== DELAY NODE DEBUG ===", [
            'contact_id' => $contactId,
            'raw_config' => $config,
            'duration_value' => $duration,
            'unit' => $unit,
            'flow_data_exists' => !is_null($flowData),
            'current_step' => $flowData ? $flowData->current_step : null,
        ]);

        // Convert to minutes based on unit
        if ($unit === 'seconds') {
            $delaySeconds = (int) $duration;
        } elseif ($unit === 'hours') {
            $delaySeconds = (int) $duration * 3600;
        } else {
            // default: minutes
            $delaySeconds = (int) $duration * 60;
        }

        Log::info("DELAY: Will delay for {$delaySeconds} seconds ({$duration} {$unit}) for contact {$contactId}");
        
        // If we have flow data, schedule a delayed job to continue the flow
        if ($flowData && $contactId) {
            // Determine the active queue driver
            $queueDriver = config('queue.default') ?? env('QUEUE_CONNECTION', 'sync');

            // If the app is using the synchronous queue driver, dispatch->delay will execute immediately.
            // In that case, perform a synchronous fallback: sleep for the duration and then continue the flow.
            if ($queueDriver === 'sync' || env('QUEUE_CONNECTION') === 'sync') {
                Log::info("Queue driver is 'sync' — performing synchronous delay fallback for contact {$contactId} for {$duration} {$unit}");
                sleep($delaySeconds);

                try {
                    $flowExecutionService = new \Modules\FlowBuilder\Services\FlowExecutionService($this->organizationId);
                    $flowExecutionService->continueDelayedFlow($contactId, $flowData->flow_id, $flowData->current_step);
                } catch (\Exception $e) {
                    Log::error("Error continuing delayed flow after sleep: " . $e->getMessage());
                }

                return 'delayed';
            }

            // Create a delayed job that will continue the flow after the delay
            Log::info("DELAY: Dispatching ProcessDelayedFlowJob with {$delaySeconds}s delay (now = " . now()->toDateTimeString() . ", will run at = " . now()->addSeconds($delaySeconds)->toDateTimeString() . ")", [
                'contact_id' => $contactId,
                'flow_id' => $flowData->flow_id,
                'current_step' => $flowData->current_step,
            ]);

            \Modules\FlowBuilder\Jobs\ProcessDelayedFlowJob::dispatch($contactId, $flowData->flow_id, $flowData->current_step, $this->organizationId)
                ->delay(now()->addSeconds($delaySeconds));
            
            return 'delayed'; // Return special value to indicate the flow is paused
        }
        
        // Fallback: simple sleep for testing (not recommended for production)
        sleep($delaySeconds);
        
        return true;
    }

    /**
     * Execute webhook action
     */
    private function executeWebhook($config, $contact, $userMessage)
    {
        $url = trim($config['url'] ?? '');
        $method = strtoupper(trim($config['method'] ?? 'POST'));

        if (!$url) {
            Log::warning("No URL specified for webhook action");
            return false;
        }

        // Prepare payload
        $payload = [
            'contact' => [
                'uuid' => $contact->uuid,
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'address' => $contact->address ? json_decode($contact->address, true) : [],
                'metadata' => $contact->metadata ? json_decode($contact->metadata, true) : [],
            ],
            'message' => $userMessage,
            'timestamp' => now()->toISOString(),
        ];

        try {
            $response = Http::timeout(30)->send($method, $url, [
                'json' => $payload,
            ]);

            if ($response->successful()) {
                // Log::info("Webhook executed successfully: {$url}");
                return true;
            } else {
                Log::warning("Webhook failed with status {$response->status()}: {$url}");
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Webhook execution error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute AI response action
     */
    private function executeAIResponse($config, $contact, $userMessage)
    {
        $prompt = $config['prompt'] ?? '';

        // Replace placeholders in prompt
        $prompt = $this->replacePlaceholders($contact, $prompt);

        // Combine user message with any prompt context
        $query = $userMessage;
        if ($prompt) {
            $query = $prompt . "\n\nUser question: " . $userMessage;
        }

        try {
            $orgConfig = $this->organization->metadata ? json_decode($this->organization->metadata, true) : [];
            $aiSettings = $orgConfig['ai'] ?? [];
            
            $settings = \App\Models\Setting::whereIn('key', [
                'default_open_ai_key',
                'default_open_ai_model',
                'enable_api_key_input',
                'enable_model_selection',
            ])->pluck('value', 'key');

            $enableApiKeyInput = $settings->get('enable_api_key_input', 1);
            $defaultOpenAiKey = $settings->get('default_open_ai_key', $aiSettings['api_key'] ?? '');
            $apiKey = $enableApiKeyInput == 1 ? ($aiSettings['api_key'] ?? '') : $defaultOpenAiKey;

            $enableModelSelection = $settings->get('enable_model_selection', 1);
            $defaultModel = $settings->get('default_open_ai_model', $aiSettings['model'] ?? 'gpt-4o-mini');
            $model = $enableModelSelection == 1 ? ($aiSettings['model'] ?? 'gpt-4o-mini') : $defaultModel;

            if (!$apiKey) {
                Log::warning("No API key for AI response in org {$this->organizationId}");
                return false;
            }

            // Use RAG pipeline to find relevant context
            $ragService = new RagService();
            $ragResult = $ragService->retrieveContext($this->organizationId, $apiKey, $query, 5);

            // Build messages array
            $messages = [];
            if ($ragResult['success']) {
                $messages[] = ['role' => 'system', 'content' => $ragResult['system_prompt']];
            } else {
                $messages[] = ['role' => 'system', 'content' => 'You are a helpful customer support assistant. Answer the user\'s question concisely.'];
            }
            $messages[] = ['role' => 'user', 'content' => $userMessage];

            // Call OpenAI
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
            ]);

            $data = $response->json();
            $aiResponse = $data['choices'][0]['message']['content'] ?? null;

            if (!$aiResponse) {
                Log::warning("Empty AI response for contact {$contact->id}");
                return false;
            }

            // Send AI response via WhatsApp
            $this->whatsappService->sendMessage($contact->uuid, $aiResponse);
            return true;
        } catch (\Exception $e) {
            Log::error("AI response execution error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute conditional action
     */
    private function executeConditional($config, $contact, $userMessage)
    {
        $conditionType = $config['condition_type'] ?? 'message_contains';
        $conditions = $config['conditions'] ?? [];
        $fieldName = $config['field_name'] ?? '';
        $fieldOperator = $config['field_operator'] ?? 'equals';

        // This method should return the condition that matches
        // The actual flow branching logic is handled in FlowExecutionService
        foreach ($conditions as $index => $condition) {
            $conditionValue = $condition['value'] ?? '';
            
            if ($this->evaluateCondition($conditionType, $conditionValue, $userMessage, $contact, $fieldName, $fieldOperator)) {
                // Log::info("Conditional action: condition {$index} matched");
                return $index; // Return the condition index that matched
            }
        }

        // Log::info("Conditional action: no conditions matched, using default");
        return 'default'; // Return 'default' if no conditions match
    }

    /**
     * Evaluate a single condition
     */
    private function evaluateCondition($conditionType, $conditionValue, $userMessage, $contact, $fieldName, $fieldOperator)
    {
        switch ($conditionType) {
            case 'message_contains':
                return stripos($userMessage, $conditionValue) !== false;
            
            case 'message_equals':
                return strtolower(trim($userMessage)) === strtolower(trim($conditionValue));
            
            case 'contact_field':
                if (!$fieldName) return false;
                
                $fieldValue = $this->getContactFieldValue($contact, $fieldName);
                return $this->compareValues($fieldValue, $fieldOperator, $conditionValue);
            

            
            default:
                return false;
        }
    }

    /**
     * Get contact field value
     */
    private function getContactFieldValue($contact, $fieldName)
    {
        $fieldMapping = [
            'first_name' => $contact->first_name,
            'last_name' => $contact->last_name,
            'email' => $contact->email,
            'phone' => $contact->phone,
        ];

        if (isset($fieldMapping[$fieldName])) {
            return $fieldMapping[$fieldName];
        }

        // Handle address fields
        if (str_starts_with($fieldName, 'address.')) {
            $address = $contact->address ? json_decode($contact->address, true) : [];
            $key = str_replace('address.', '', $fieldName);
            return $address[$key] ?? '';
        }

        // Handle metadata fields
        $metadata = $contact->metadata ? json_decode($contact->metadata, true) : [];
        return $metadata[$fieldName] ?? '';
    }

    /**
     * Compare values based on operator
     */
    private function compareValues($fieldValue, $operator, $conditionValue)
    {
        switch ($operator) {
            case 'equals':
                return strtolower(trim($fieldValue)) === strtolower(trim($conditionValue));
            
            case 'contains':
                return stripos($fieldValue, $conditionValue) !== false;
            
            case 'not_equals':
                return strtolower(trim($fieldValue)) !== strtolower(trim($conditionValue));
            
            case 'is_empty':
                return empty(trim($fieldValue));
            
            case 'is_not_empty':
                return !empty(trim($fieldValue));
            
            default:
                return false;
        }
    }

    /**
     * Replace placeholders in text
     */
    private function replacePlaceholders($contact, $text)
    {
        $address = $contact->address ? json_decode($contact->address, true) : [];
        $metadata = $contact->metadata ? json_decode($contact->metadata, true) : [];
        
        $fullAddress = ($address['street'] ?? '') . ', ' .
                      ($address['city'] ?? '') . ', ' .
                      ($address['state'] ?? '') . ', ' .
                      ($address['zip'] ?? '') . ', ' .
                      ($address['country'] ?? '');

        $placeholders = [
            '{first_name}' => $contact->first_name ?? '',
            '{last_name}' => $contact->last_name ?? '',
            '{full_name}' => $contact->full_name ?? '',
            '{email}' => $contact->email ?? '',
            '{phone}' => $contact->phone ?? '',
            '{phone_number}' => $contact->phone ?? '',
            '{organization_name}' => $this->organization->name ?? '',
            '{full_address}' => $fullAddress,
            '{street}' => $address['street'] ?? '',
            '{city}' => $address['city'] ?? '',
            '{state}' => $address['state'] ?? '',
            '{zip_code}' => $address['zip'] ?? '',
            '{country}' => $address['country'] ?? '',
        ];

        // Add metadata placeholders
        foreach ($metadata as $key => $value) {
            $transformedKey = strtolower(str_replace(' ', '_', $key));
            $placeholders['{' . $transformedKey . '}'] = $value;
        }

        return str_replace(array_keys($placeholders), array_values($placeholders), $text);
    }
} 