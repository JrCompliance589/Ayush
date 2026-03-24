<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;
use App\Services\SubscriptionService;
use App\Services\LifetimeSubscriptionService;

class CampaignLimit implements Rule
{
    protected $ignoreId;
    protected $errorMessage;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
        $this->errorMessage = __('You have reached your limit of campaigns. Please upgrade your account to add more!');
    }
    
    public function passes($attribute, $value)
    {
        $organizationId = session()->get('current_organization');

        // Check if user has lifetime subscription
        if (LifetimeSubscriptionService::hasLifetimeSubscription($organizationId)) {
            // Check daily campaign limit for lifetime subscribers
            $check = LifetimeSubscriptionService::canCreateCampaign($organizationId);
            if (!$check['allowed']) {
                $this->errorMessage = __('You have reached your daily campaign limit. Purchase addons to increase your limit or wait until tomorrow.');
                return false;
            }
            return true;
        }

        // For non-lifetime users, check the regular subscription limit
        return !SubscriptionService::isSubscriptionFeatureLimitReached($organizationId, 'campaign_limit');
    }

    public function message()
    {
        return $this->errorMessage;
    }
}
