<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;
use App\Services\SubscriptionService;
use App\Services\LifetimeSubscriptionService;

class ContactLimit implements Rule
{
    protected $ignoreId;
    protected $errorMessage;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
        $this->errorMessage = __('You have reached your limit of contacts. Please upgrade your account to add more!');
    }
    
    public function passes($attribute, $value)
    {
        $organizationId = session()->get('current_organization');

        // Check if user has lifetime subscription
        if (LifetimeSubscriptionService::hasLifetimeSubscription($organizationId)) {
            // Check daily contacts limit for lifetime subscribers
            $check = LifetimeSubscriptionService::canUseContacts($organizationId, 1);
            if (!$check['allowed']) {
                $this->errorMessage = __('You have reached your daily contacts limit. Purchase addons to increase your limit or wait until tomorrow.');
                return false;
            }
            return true;
        }

        // For non-lifetime users, check the regular subscription limit
        return !SubscriptionService::isSubscriptionFeatureLimitReached($organizationId, 'contacts_limit');
    }

    public function message()
    {
        return $this->errorMessage;
    }
}
