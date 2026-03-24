<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DailyUsageLog extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $timestamps = true;

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'usage_date' => 'date',
        'campaigns_created' => 'integer',
        'contacts_used' => 'integer',
    ];

    /**
     * Get the organization that owns this usage log.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Get or create today's usage log for an organization.
     */
    public static function getOrCreateToday($organizationId)
    {
        return self::firstOrCreate(
            [
                'organization_id' => $organizationId,
                'usage_date' => Carbon::today()->toDateString(),
            ],
            [
                'campaigns_created' => 0,
                'contacts_used' => 0,
            ]
        );
    }

    /**
     * Increment campaign count for today.
     */
    public static function incrementCampaigns($organizationId, $count = 1)
    {
        $log = self::getOrCreateToday($organizationId);
        $log->increment('campaigns_created', $count);
        return $log;
    }

    /**
     * Increment contacts used for today.
     */
    public static function incrementContactsUsed($organizationId, $count)
    {
        $log = self::getOrCreateToday($organizationId);
        $log->increment('contacts_used', $count);
        return $log;
    }

    /**
     * Get today's campaign count for an organization.
     */
    public static function getTodayCampaignsCount($organizationId)
    {
        $log = self::where('organization_id', $organizationId)
            ->where('usage_date', Carbon::today()->toDateString())
            ->first();

        return $log ? $log->campaigns_created : 0;
    }

    /**
     * Get today's contacts used count for an organization.
     */
    public static function getTodayContactsUsed($organizationId)
    {
        $log = self::where('organization_id', $organizationId)
            ->where('usage_date', Carbon::today()->toDateString())
            ->first();

        return $log ? $log->contacts_used : 0;
    }

    /**
     * Check if organization can create campaign today based on limit.
     */
    public static function canCreateCampaign($organizationId, $dailyLimit)
    {
        if ($dailyLimit == -1) {
            return true; // Unlimited
        }

        return self::getTodayCampaignsCount($organizationId) < $dailyLimit;
    }

    /**
     * Check if organization can use contacts based on daily limit.
     */
    public static function canUseContacts($organizationId, $dailyLimit, $contactsNeeded)
    {
        if ($dailyLimit == -1) {
            return true; // Unlimited
        }

        $usedToday = self::getTodayContactsUsed($organizationId);
        return ($usedToday + $contactsNeeded) <= $dailyLimit;
    }

    /**
     * Get remaining campaigns for today.
     */
    public static function getRemainingCampaigns($organizationId, $dailyLimit)
    {
        if ($dailyLimit == -1) {
            return -1; // Unlimited
        }

        $used = self::getTodayCampaignsCount($organizationId);
        return max(0, $dailyLimit - $used);
    }

    /**
     * Get remaining contacts for today.
     */
    public static function getRemainingContacts($organizationId, $dailyLimit)
    {
        if ($dailyLimit == -1) {
            return -1; // Unlimited
        }

        $used = self::getTodayContactsUsed($organizationId);
        return max(0, $dailyLimit - $used);
    }
}
