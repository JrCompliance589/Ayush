<?php

namespace App\Services;

use App\Models\DailyUsageLog;
use App\Models\LifetimeAddon;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\UserLifetimeAddon;
use App\Models\User;
use App\Models\BalanceHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LifetimeSubscriptionService
{
    /**
     * Check if organization has a lifetime subscription.
     */
    public static function hasLifetimeSubscription($organizationId)
    {
        $subscription = Subscription::where('organization_id', $organizationId)->first();
        
        if (!$subscription || !$subscription->plan_id) {
            return false;
        }

        $plan = SubscriptionPlan::find($subscription->plan_id);
        return $plan && $plan->period === 'lifetime';
    }

    /**
     * Get lifetime plan limits for an organization.
     */
    public static function getLifetimePlanLimits($organizationId)
    {
        $subscription = Subscription::where('organization_id', $organizationId)->first();
        
        if (!$subscription || !$subscription->plan_id) {
            return null;
        }

        $plan = SubscriptionPlan::find($subscription->plan_id);
        
        if (!$plan || $plan->period !== 'lifetime') {
            return null;
        }

        $metadata = json_decode($plan->metadata, true);

        return [
            'daily_campaign_limit' => $metadata['daily_campaign_limit'] ?? -1,
            'daily_contacts_limit' => $metadata['daily_contacts_limit'] ?? -1,
            'campaign_limit' => $metadata['campaign_limit'] ?? -1,
            'contacts_limit' => $metadata['contacts_limit'] ?? -1,
            'message_limit' => $metadata['message_limit'] ?? -1,
        ];
    }

    /**
     * Get effective daily limits including purchased addons.
     */
    public static function getEffectiveDailyLimits($organizationId)
    {
        $baseLimits = self::getLifetimePlanLimits($organizationId);

        if (!$baseLimits) {
            return null;
        }

        // Get additional limits from purchased addons
        $additionalCampaigns = UserLifetimeAddon::getTotalQuantityByType($organizationId, 'campaign_limit');
        $additionalContacts = UserLifetimeAddon::getTotalQuantityByType($organizationId, 'contacts_limit');

        // Add purchased addon quantities to daily limits
        $effectiveCampaignLimit = $baseLimits['daily_campaign_limit'];
        $effectiveContactsLimit = $baseLimits['daily_contacts_limit'];

        if ($effectiveCampaignLimit != -1) {
            $effectiveCampaignLimit += $additionalCampaigns;
        }

        if ($effectiveContactsLimit != -1) {
            $effectiveContactsLimit += $additionalContacts;
        }

        return [
            'daily_campaign_limit' => $effectiveCampaignLimit,
            'daily_contacts_limit' => $effectiveContactsLimit,
            'additional_campaigns' => $additionalCampaigns,
            'additional_contacts' => $additionalContacts,
        ];
    }

    /**
     * Check if organization can create a campaign today.
     */
    public static function canCreateCampaign($organizationId)
    {
        if (!self::hasLifetimeSubscription($organizationId)) {
            return ['allowed' => true, 'message' => 'Not a lifetime subscription'];
        }

        $limits = self::getEffectiveDailyLimits($organizationId);

        if (!$limits) {
            return ['allowed' => false, 'message' => 'No limits found'];
        }

        $canCreate = DailyUsageLog::canCreateCampaign($organizationId, $limits['daily_campaign_limit']);
        $remaining = DailyUsageLog::getRemainingCampaigns($organizationId, $limits['daily_campaign_limit']);

        return [
            'allowed' => $canCreate,
            'remaining' => $remaining,
            'daily_limit' => $limits['daily_campaign_limit'],
            'message' => $canCreate ? 'Can create campaign' : 'Daily campaign limit reached',
        ];
    }

    /**
     * Check if organization can use specified number of contacts today.
     */
    public static function canUseContacts($organizationId, $contactsNeeded)
    {
        if (!self::hasLifetimeSubscription($organizationId)) {
            return ['allowed' => true, 'message' => 'Not a lifetime subscription'];
        }

        $limits = self::getEffectiveDailyLimits($organizationId);

        if (!$limits) {
            return ['allowed' => false, 'message' => 'No limits found'];
        }

        $canUse = DailyUsageLog::canUseContacts($organizationId, $limits['daily_contacts_limit'], $contactsNeeded);
        $remaining = DailyUsageLog::getRemainingContacts($organizationId, $limits['daily_contacts_limit']);

        return [
            'allowed' => $canUse,
            'remaining' => $remaining,
            'daily_limit' => $limits['daily_contacts_limit'],
            'contacts_needed' => $contactsNeeded,
            'message' => $canUse ? 'Can use contacts' : 'Daily contacts limit would be exceeded',
        ];
    }

    /**
     * Record campaign creation for daily tracking.
     * Note: Only tracks campaigns, not contacts. Contacts are tracked separately when adding new contacts.
     */
    public static function recordCampaignCreation($organizationId)
    {
        if (!self::hasLifetimeSubscription($organizationId)) {
            return;
        }

        DailyUsageLog::incrementCampaigns($organizationId);

        Log::info('Lifetime subscription campaign usage recorded', [
            'organization_id' => $organizationId,
        ]);
    }

    /**
     * Record contact addition for daily tracking.
     */
    public static function recordContactAddition($organizationId, $contactsCount = 1)
    {
        if (!self::hasLifetimeSubscription($organizationId)) {
            return;
        }

        DailyUsageLog::incrementContactsUsed($organizationId, $contactsCount);

        Log::info('Lifetime subscription contacts usage recorded', [
            'organization_id' => $organizationId,
            'contacts_count' => $contactsCount,
        ]);
    }

    /**
     * Get today's usage summary for an organization.
     */
    public static function getTodayUsageSummary($organizationId)
    {
        $limits = self::getEffectiveDailyLimits($organizationId);

        if (!$limits) {
            return null;
        }

        return [
            'campaigns_created_today' => DailyUsageLog::getTodayCampaignsCount($organizationId),
            'contacts_used_today' => DailyUsageLog::getTodayContactsUsed($organizationId),
            'daily_campaign_limit' => $limits['daily_campaign_limit'],
            'daily_contacts_limit' => $limits['daily_contacts_limit'],
            'remaining_campaigns' => DailyUsageLog::getRemainingCampaigns($organizationId, $limits['daily_campaign_limit']),
            'remaining_contacts' => DailyUsageLog::getRemainingContacts($organizationId, $limits['daily_contacts_limit']),
            'additional_campaigns' => $limits['additional_campaigns'],
            'additional_contacts' => $limits['additional_contacts'],
        ];
    }

    /**
     * Deduct message cost from user wallet.
     */
    public static function deductMessageCost($userId, $amount, $description = 'Message cost deduction')
    {
        return DB::transaction(function () use ($userId, $amount, $description) {
            $user = User::findOrFail($userId);

            if ($user->balance < $amount) {
                Log::warning('Insufficient wallet balance for message', [
                    'user_id' => $userId,
                    'required' => $amount,
                    'available' => $user->balance,
                ]);
                return [
                    'success' => false,
                    'message' => 'Insufficient wallet balance',
                    'required' => $amount,
                    'available' => $user->balance,
                ];
            }

            $user->balance -= $amount;
            $user->save();

            BalanceHistory::create([
                'user_id' => $userId,
                'amount' => $amount,
                'balance_after' => $user->balance,
                'type' => 'debit',
                'note' => $description,
            ]);

            return [
                'success' => true,
                'message' => 'Amount deducted successfully',
                'amount' => $amount,
                'new_balance' => $user->balance,
            ];
        });
    }

    /**
     * Calculate lifetime subscription validity (15 years from start date).
     */
    public static function calculateLifetimeValidity($startDate = null)
    {
        $start = $startDate ? Carbon::parse($startDate) : Carbon::now();
        return $start->addYears(15)->toDateTimeString();
    }

    /**
     * Get available lifetime addons for purchase.
     */
    public static function getAvailableAddons()
    {
        return LifetimeAddon::active()
            ->whereNull('deleted_at')
            ->get();
    }

    /**
     * Purchase an addon for an organization.
     */
    public static function purchaseAddon($organizationId, $addonId, $quantity, $paymentId = null)
    {
        return DB::transaction(function () use ($organizationId, $addonId, $quantity, $paymentId) {
            $addon = LifetimeAddon::findOrFail($addonId);

            $totalPrice = $addon->price * $quantity;
            $totalQuantity = $addon->quantity * $quantity;

            $purchase = UserLifetimeAddon::create([
                'organization_id' => $organizationId,
                'addon_id' => $addonId,
                'quantity' => $totalQuantity,
                'price_paid' => $totalPrice,
                'payment_id' => $paymentId,
                'status' => $paymentId ? 'completed' : 'pending',
            ]);

            Log::info('Lifetime addon purchased', [
                'organization_id' => $organizationId,
                'addon_id' => $addonId,
                'quantity' => $totalQuantity,
                'price' => $totalPrice,
                'payment_id' => $paymentId,
            ]);

            return $purchase;
        });
    }

    /**
     * Complete a pending addon purchase.
     */
    public static function completePurchase($purchaseId, $paymentId)
    {
        $purchase = UserLifetimeAddon::findOrFail($purchaseId);
        $purchase->update([
            'payment_id' => $paymentId,
            'status' => 'completed',
        ]);

        Log::info('Lifetime addon purchase completed', [
            'purchase_id' => $purchaseId,
            'payment_id' => $paymentId,
        ]);

        return $purchase;
    }

    /**
     * Get organization's purchased addons.
     */
    public static function getOrganizationAddons($organizationId)
    {
        return UserLifetimeAddon::with('addon')
            ->where('organization_id', $organizationId)
            ->where('status', 'completed')
            ->get();
    }
}
