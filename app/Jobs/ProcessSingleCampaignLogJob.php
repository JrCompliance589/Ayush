<?php

namespace App\Jobs;

use App\Jobs\RetryCampaignLogJob;
use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\CampaignLogRetry;
use App\Models\Organization;
use App\Services\WhatsappService;
use App\Traits\TemplateTrait;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessSingleCampaignLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TemplateTrait, Batchable;

    private $campaignLog;
    private $organizationId;
    private $whatsappService;
    
    public $timeout = 300; // 5 minutes timeout for single message
    public $tries = 3;

    public function __construct(CampaignLog $campaignLog)
    {
        $this->campaignLog = $campaignLog;
    }

    public function handle()
    {
        try {
            DB::transaction(function() {
                // Lock the log for update to prevent duplicate processing
                $lockedLog = CampaignLog::where('id', $this->campaignLog->id)
                    ->whereIn('status', ['pending', 'failed'])
                    ->lockForUpdate()
                    ->first();
                       
                if ($lockedLog) {
                    // Check if contact exists
                    if (!$this->campaignLog->contact) {
                        $lockedLog->status = 'failed';
                        $lockedLog->metadata = json_encode(['error' => 'Contact not found or deleted']);
                        $lockedLog->save();
                        return;
                    }

                    $campaign_user_id = Campaign::find($this->campaignLog->campaign_id)?->created_by;
                    $lockedLog->status = 'ongoing';
                    $lockedLog->save();
            
                    $this->organizationId = $this->campaignLog->campaign->organization_id;
                    $this->initializeWhatsappService();
            
                    $template = $this->buildTemplateRequest($this->campaignLog->campaign_id, $this->campaignLog->contact);
                    $responseObject = $this->whatsappService->sendTemplateMessage(
                        $this->campaignLog->contact->uuid, 
                        $template, 
                        $campaign_user_id, 
                        $this->campaignLog->campaign_id
                    );

                    $this->updateLogStatus($lockedLog, $responseObject);
                }
            });
        } catch (\Exception $e) {
            Log::error('Error processing campaign log ' . $this->campaignLog->id . ': ' . $e->getMessage());
            throw $e;
        }
    }

    protected function updateLogStatus(CampaignLog $log, $responseObject)
    {
        $log->chat_id = $responseObject->data->chat->id ?? null;
        $log->status = ($responseObject->success === true) ? 'success' : 'failed';
        
        // Clean up response object
        unset($responseObject->success);
        if (property_exists($responseObject, 'data') && property_exists($responseObject->data, 'chat')) {
            unset($responseObject->data->chat);
        }
        
        $log->metadata = json_encode($responseObject);
        $log->updated_at = now();
        $log->save();

        // Check if campaign is completed
        $this->checkAndUpdateCampaignStatus($log->campaign_id);
    }

    protected function checkAndUpdateCampaignStatus($campaignId)
    {
        $campaign = Campaign::find($campaignId);
        $orgMetadata = json_decode($campaign->organization->metadata ?? '{}', true);
        $retryEnabled = $orgMetadata['campaigns']['enable_resend'] ?? false;
        $retryIntervals = $orgMetadata['campaigns']['resend_intervals'] ?? [];
        $intervalUnit = $orgMetadata['campaigns']['resend_interval_unit'] ?? 'seconds';
    
        $pendingOrOngoing = CampaignLog::where('campaign_id', $campaignId)
            ->whereIn('status', ['pending', 'ongoing'])
            ->exists();

        if ($pendingOrOngoing) {
            return; // Still processing logs
        }

        if ($retryEnabled) {
            $logs = CampaignLog::where('campaign_id', $campaignId)->get();
    
            foreach ($logs as $log) {
                $retryCount = $log->retries()->count();
                $maxRetries = count($retryIntervals);

                // Skip if already retried max times
                if ($retryCount >= $maxRetries) {
                    continue;
                }

                // If log failed and hasn't reached max retries, schedule retry
                if ($log->status === 'failed' && $retryCount < $maxRetries) {
                    $delayValue = $retryIntervals[$retryCount] ?? null;

                    if ($delayValue !== null) {
                        $delay = $this->calculateDelay($delayValue, $intervalUnit);
                        Log::info("Scheduling campaign retry", [
                            'campaign_id' => $campaignId,
                            'campaign_log_id' => $log->id,
                            'contact_id' => $log->contact_id,
                            'retry_index' => $retryCount,
                            'delay_value' => $delayValue,
                            'delay_unit' => $intervalUnit
                        ]);
                        RetryCampaignLogJob::dispatch($campaign->organization_id, $log->id, $retryCount)->onQueue('campaign-messages')->delay($delay);
                    }
                }  
            }
        } else {
            // Check again after potential changes
            $unprocessed = CampaignLog::where('campaign_id', $campaignId)
                ->whereIn('status', ['pending', 'ongoing'])
                ->exists();

            if (!$unprocessed) {
                $campaign->update(['status' => 'completed']);
                $campaign->snapshotStats();
            }
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

    /*private function initializeWhatsappService()
    {
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
    }*/

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