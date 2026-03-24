<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\Chat;
use App\Models\Organization;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MoveFailedCampaignContacts extends Command
{
    protected $signature = 'campaigns:move-failed-contacts {--org= : Organization ID to process}';
    protected $description = 'Move contacts with exhausted retries and failed status to the failed contacts group';

    public function handle()
    {
        $orgId = $this->option('org');

        $organizations = $orgId 
            ? Organization::where('id', $orgId)->get()
            : Organization::all();

        foreach ($organizations as $organization) {
            $this->processOrganization($organization);
        }

        $this->info('Done!');
    }

    protected function processOrganization(Organization $organization)
    {
        $metadata = json_decode($organization->metadata ?? '{}', true);
        $campaignSettings = $metadata['campaigns'] ?? [];
        
        $moveToGroup = $campaignSettings['move_failed_contacts_to_group'] ?? false;
        $failedGroupUuid = $campaignSettings['failed_campaign_group'] ?? null;
        $retryEnabled = $campaignSettings['enable_resend'] ?? false;
        $retryIntervals = $campaignSettings['resend_intervals'] ?? [];
        $maxRetries = count($retryIntervals);

        if (!$moveToGroup || !$failedGroupUuid) {
            $this->info("Organization {$organization->id}: Feature not enabled, skipping");
            return;
        }

        $failedGroupId = DB::table('contact_groups')
            ->where('uuid', $failedGroupUuid)
            ->value('id');

        if (!$failedGroupId) {
            $this->warn("Organization {$organization->id}: Failed group not found with UUID {$failedGroupUuid}");
            return;
        }

        $this->info("Organization {$organization->id}: Processing (max retries: {$maxRetries})");

        // Get campaigns for this organization
        $campaigns = Campaign::where('organization_id', $organization->id)
            ->pluck('id');

        if ($campaigns->isEmpty()) {
            $this->info("  No campaigns found");
            return;
        }

        // Find logs that should be moved
        $contactsMoved = 0;

        CampaignLog::with(['retries', 'chat'])
            ->whereIn('campaign_id', $campaigns)
            ->chunk(500, function ($logs) use ($maxRetries, $retryEnabled, $failedGroupId, &$contactsMoved) {
                foreach ($logs as $log) {
                    $retryCount = $log->retries->count();

                    // Check if should move: retry disabled OR max retries exhausted
                    $shouldCheck = !$retryEnabled || $retryCount >= $maxRetries;

                    if (!$shouldCheck) {
                        continue;
                    }

                    // Check if last retry chat is failed (or original chat if no retries)
                    $isFailed = false;
                    
                    if ($retryCount > 0) {
                        $lastRetry = $log->retries->sortByDesc('id')->first();
                        if ($lastRetry && $lastRetry->chat_id) {
                            $lastRetryChat = Chat::find($lastRetry->chat_id);
                            $isFailed = $lastRetryChat && $lastRetryChat->status === 'failed';
                        }
                    } else {
                        $isFailed = $log->chat && $log->chat->status === 'failed';
                    }

                    if (!$isFailed) {
                        continue;
                    }

                    // Check if contact already in group
                    $exists = DB::table('contact_contact_group')
                        ->where('contact_id', $log->contact_id)
                        ->where('contact_group_id', $failedGroupId)
                        ->exists();

                    if (!$exists) {
                        DB::table('contact_contact_group')->insert([
                            'contact_id' => $log->contact_id,
                            'contact_group_id' => $failedGroupId,
                        ]);
                        $contactsMoved++;
                    }
                }
            });

        $this->info("  Moved {$contactsMoved} contacts to failed group");
    }
}
