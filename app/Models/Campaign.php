<?php

namespace App\Models;

use App\Helpers\DateTimeHelper;
use App\Http\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model {
    use HasFactory;
    use HasUuid;

    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'stats_cache' => 'array',
    ];

    public function getCreatedAtAttribute($value)
    {
        return DateTimeHelper::convertToOrganizationTimezone($value)->toDateTimeString();
    }

    public function getDeletedAtAttribute($value)
    {
        return DateTimeHelper::convertToOrganizationTimezone($value)->toDateTimeString();
    }

    public function getScheduledAtAttribute($value)
    {
        return DateTimeHelper::convertToOrganizationTimezone($value)->toDateTimeString();
    }

    public function organization(){
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function template(){
        return $this->belongsTo(Template::class, 'template_id', 'id');
    }

    public function contactGroup(){
        return $this->belongsTo(ContactGroup::class, 'contact_group_id', 'id');
    }

    public function campaignLogs(){
        return $this->hasMany(CampaignLog::class, 'campaign_id', 'id');
    }

    public function contactsCount(){
        return $this->campaignLogs->count();
    }

    public function contactGroupCount(){
        return $this->contactGroup ? $this->contactGroup->contacts->count() : 0;
    }

    public function sentCount(){
        return $this->campaignLogs()
            ->where('status', 'success')
            ->whereHas('chat', function ($query) {
                $query->whereIn('status', ['accepted', 'sent', 'delivered', 'read']);
            })
            ->count();
    }

    public function deliveryCount(){
        return $this->campaignLogs()
            ->where('status', 'success')
            ->whereHas('chat', function ($query) {
                $query->whereIn('status', ['delivered', 'read']);
            })
            ->count();
    }

    public function failedCount(){
        $failedToSendCount = $this->campaignLogs()->where('status', 'failed')->count();

        $chatFailedCount = $this->campaignLogs()
            ->where('status', 'success')
            ->whereHas('chat', function ($query) {
                $query->where('status', 'failed');
            })
            ->count();

        return $failedToSendCount + $chatFailedCount;
    }

    public function readCount(){
        return $this->campaignLogs()
            ->where('status', 'success')
            ->whereHas('chat', function ($query) {
                $query->where('status', 'read');
            })
            ->count();
    }

    public function getCounts(){
        // If campaign_logs exist, compute live stats
        $logCount = $this->campaignLogs()->count();

        if ($logCount > 0) {
            $counts = $this->campaignLogs()
                ->selectRaw('
                    COUNT(*) as total_message_count,
                    SUM(CASE WHEN campaign_logs.status = "success" AND chat.status IN ("accepted", "sent", "delivered", "read") THEN 1 ELSE 0 END) as total_sent_count,
                    SUM(CASE WHEN campaign_logs.status = "success" AND chat.status IN ("delivered", "read") THEN 1 ELSE 0 END) as total_delivered_count,
                    SUM(CASE WHEN campaign_logs.status = "failed" THEN 1 ELSE 0 END) + 
                        SUM(CASE WHEN campaign_logs.status = "success" AND chat.status = "failed" THEN 1 ELSE 0 END) as total_failed_count,
                    SUM(CASE WHEN campaign_logs.status = "success" AND chat.status = "read" THEN 1 ELSE 0 END) as total_read_count
                ')
                ->leftJoin('chats as chat', 'chat.id', '=', 'campaign_logs.chat_id')
                ->where('campaign_logs.campaign_id', $this->id)
                ->first();

            // Auto-snapshot when campaign is completed and stats haven't been cached yet
            if ($this->status === 'completed' && empty($this->stats_cache)) {
                $this->snapshotStats($counts);
            }

            return $counts;
        }

        // Fall back to cached stats if campaign_logs were pruned
        if (!empty($this->stats_cache)) {
            return (object) $this->stats_cache;
        }

        // No logs and no cache — return zeros
        return (object) [
            'total_message_count' => 0,
            'total_sent_count' => 0,
            'total_delivered_count' => 0,
            'total_failed_count' => 0,
            'total_read_count' => 0,
        ];
    }

    /**
     * Snapshot current campaign statistics into stats_cache column.
     * This preserves stats even after campaign_logs are pruned.
     */
    public function snapshotStats($counts = null)
    {
        if (!$counts) {
            $counts = $this->campaignLogs()
                ->selectRaw('
                    COUNT(*) as total_message_count,
                    SUM(CASE WHEN campaign_logs.status = "success" AND chat.status IN ("accepted", "sent", "delivered", "read") THEN 1 ELSE 0 END) as total_sent_count,
                    SUM(CASE WHEN campaign_logs.status = "success" AND chat.status IN ("delivered", "read") THEN 1 ELSE 0 END) as total_delivered_count,
                    SUM(CASE WHEN campaign_logs.status = "failed" THEN 1 ELSE 0 END) + 
                        SUM(CASE WHEN campaign_logs.status = "success" AND chat.status = "failed" THEN 1 ELSE 0 END) as total_failed_count,
                    SUM(CASE WHEN campaign_logs.status = "success" AND chat.status = "read" THEN 1 ELSE 0 END) as total_read_count
                ')
                ->leftJoin('chats as chat', 'chat.id', '=', 'campaign_logs.chat_id')
                ->where('campaign_logs.campaign_id', $this->id)
                ->first();
        }

        if ($counts && $counts->total_message_count > 0) {
            self::where('id', $this->id)->update([
                'stats_cache' => json_encode([
                    'total_message_count' => (int) $counts->total_message_count,
                    'total_sent_count' => (int) $counts->total_sent_count,
                    'total_delivered_count' => (int) $counts->total_delivered_count,
                    'total_failed_count' => (int) $counts->total_failed_count,
                    'total_read_count' => (int) $counts->total_read_count,
                ]),
            ]);
        }
    }
}
