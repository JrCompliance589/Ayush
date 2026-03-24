<?php

namespace App\Jobs;

use App\Models\CampaignLog;
use App\Models\CampaignLogRetry;
use App\Models\Organization;
use App\Services\WhatsappService;
use App\Traits\TemplateTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RetryCampaignLogJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TemplateTrait;

    public $timeout = 300;
    private $organizationId;
    private $campaignLogId;
    protected $retryIndex;

    public function __construct(int $organizationId, int $campaignLogId, int $retryIndex)
    {
        $this->organizationId = $organizationId;
        $this->campaignLogId = $campaignLogId;
        $this->retryIndex = $retryIndex;
    }

    public function uniqueId()
    {
        return $this->campaignLogId . '-' . $this->retryIndex;
    }

    public function handle()
    {
        $log = CampaignLog::with('campaign', 'campaign.organization', 'contact')->find($this->campaignLogId);
        $campaignSettings = json_decode($log->campaign->organization->metadata ?? '{}', true)['campaigns'] ?? [];
        $retryIntervals = $campaignSettings['resend_intervals'] ?? [];
        $maxRetries = count($retryIntervals);
        $retryCount = $log->retries()->count();

        Log::info("RetryCampaignLogJob started", [
            'campaign_log_id' => $this->campaignLogId,
            'retry_index' => $this->retryIndex,
            'current_retry_count' => $retryCount,
            'max_retries' => $maxRetries,
            'contact_id' => $log->contact_id ?? null
        ]);

        if (!$log || $log->status !== 'failed') {
            Log::info("RetryCampaignLogJob skipped - log not found or not failed", [
                'campaign_log_id' => $this->campaignLogId,
                'status' => $log->status ?? 'not found'
            ]);
            return;
        }

        // Note: Retries should work even for completed campaigns
        // because the retry mechanism should still process failed messages

        if($retryCount >= $maxRetries){
            Log::info("RetryCampaignLogJob skipped - max retries reached", [
                'campaign_log_id' => $this->campaignLogId,
                'retry_count' => $retryCount,
                'max_retries' => $maxRetries
            ]);
            return; //Don't process if retry count limit reached
        }

        // Initialize WhatsApp service
        $this->initializeWhatsappService();

        DB::beginTransaction();

        try {
            Log::info("RetryCampaignLogJob sending retry message", [
                'campaign_log_id' => $this->campaignLogId,
                'retry_attempt' => $retryCount + 1,
                'contact_phone' => $log->contact->phone ?? null
            ]);

            // Create retry log
            $retryLog = new CampaignLogRetry();
            $retryLog->campaign_log_id = $this->campaignLogId;
            $retryLog->status = 'ongoing';
            $retryLog->save();

            $template = $this->buildTemplateRequest($log->campaign_id, $log->contact);
            $campaign_user_id = $log->campaign->created_by;

            $response = $this->whatsappService->sendTemplateMessage(
                $log->contact->uuid,
                $template,
                $campaign_user_id,
                $log->campaign_id
            );

            $status = ($response->success === true) ? 'success' : 'failed';
            $retryLog->chat_id = $response->data->chat->id ?? null;
            $retryLog->status = $status;

            Log::info("RetryCampaignLogJob message sent", [
                'campaign_log_id' => $this->campaignLogId,
                'retry_attempt' => $retryCount + 1,
                'result_status' => $status,
                'contact_phone' => $log->contact->phone ?? null
            ]);

            // Clean metadata
            unset($response->success);
            if (property_exists($response, 'data') && property_exists($response->data, 'chat')) {
                unset($response->data->chat);
            }

            $retryLog->metadata = json_encode($response);
            $retryLog->save();

            // Update the retry_count on the original campaign log
            $log->retry_count += 1;
            $log->status = $status;
            $log->save();

            // Check if it failed and we need to schedule next retry
            if ($retryLog->status === 'failed') {
                $intervals = $campaignSettings['resend_intervals'] ?? [];
                $intervalUnit = $campaignSettings['resend_interval_unit'] ?? 'seconds';

                if (isset($intervals[$this->retryIndex])) {
                    if($this->retryIndex + 1 < $maxRetries){
                        $nextInterval = $intervals[$this->retryIndex + 1];
                        $delay = $this->calculateDelay($nextInterval, $intervalUnit);
                        Log::info("RetryCampaignLogJob scheduling next retry", [
                            'campaign_log_id' => $this->campaignLogId,
                            'next_retry_index' => $this->retryIndex + 1,
                            'delay_value' => $nextInterval,
                            'delay_unit' => $intervalUnit
                        ]);
                        self::dispatch($this->organizationId, $log->id, $this->retryIndex + 1)->onQueue('campaign-messages')->delay($delay);
                    }
                } 
                
                if (!empty($campaignSettings['move_failed_contacts_to_group']) && $retryCount >= $maxRetries) {
                    $groupUuid = $campaignSettings['failed_campaign_group'];
                    $failedGroupId = DB::table('contact_groups')->where('uuid', $groupUuid)->value('id');

                    // Check if the group exists in the contact_groups table by UUID
                    if (!$failedGroupId) {
                        Log::warning('Failed to move contact: Group with UUID ' . $groupUuid . ' does not exist.');
                        return;
                    }

                    // Remove all groups for the contact
                    DB::table('contact_contact_group')
                        ->where('contact_id', $log->contact_id)
                        ->delete();

                    // Add contact to the failed group
                    DB::table('contact_contact_group')->insert([
                        'contact_id' => $log->contact_id,
                        'contact_group_id' => $failedGroupId, // Use the group ID here
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Retry failed for campaign_log {$this->campaignLogId}: " . $e->getMessage());
        }
    }

    private function initializeWhatsappService()
    {
        $config = cache()->remember("organization.{$this->organizationId}.metadata", 3600, function() {
            return Organization::find($this->organizationId)->metadata ?? [];
        });

        $config = Organization::where('id', $this->organizationId)->first()->metadata;
        $config = $config ? json_decode($config, true) : [];

        $accessToken = $config['whatsapp']['access_token'] ?? null;
        $apiVersion = 'v18.0';
        $appId = $config['whatsapp']['app_id'] ?? null;
        $phoneNumberId = $config['whatsapp']['phone_number_id'] ?? null;
        $wabaId = $config['whatsapp']['waba_id'] ?? null;

        $this->whatsappService = new WhatsappService(
            $accessToken, 
            $apiVersion, 
            $appId, 
            $phoneNumberId, 
            $wabaId, 
            $this->organizationId
        );
    }

    /**
     * Calculate delay based on interval value and unit
     */
    private function calculateDelay(int $interval, string $unit): \DateTime
    {
        return match($unit) {
            'seconds' => now()->addSeconds($interval),
            'minutes' => now()->addMinutes($interval),
            'hours' => now()->addHours($interval),
            default => now()->addSeconds($interval),
        };
    }
}
