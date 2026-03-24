<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SnapshotCampaignStats extends Command
{
    protected $signature = 'campaigns:snapshot-stats';
    protected $description = 'Snapshot campaign statistics before campaign_logs are pruned';

    public function handle()
    {
        $campaigns = Campaign::whereIn('status', ['completed', 'ongoing'])
            ->whereNull('stats_cache')
            ->whereHas('campaignLogs')
            ->cursor();

        $count = 0;
        foreach ($campaigns as $campaign) {
            try {
                $campaign->snapshotStats();
                $count++;
            } catch (\Exception $e) {
                Log::error("Failed to snapshot stats for campaign {$campaign->id}: " . $e->getMessage());
            }
        }

        $this->info("Snapshotted stats for {$count} campaigns.");
        Log::info("SnapshotCampaignStats: Snapshotted stats for {$count} campaigns.");
    }
}
