<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use DB;
use App\Http\Controllers\Controller as BaseController;
use App\Helpers\CustomHelper;
use App\Helpers\SubscriptionHelper;
use App\Models\Addon;
use App\Models\Chat;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\Template;
use App\Models\Team;
use App\Services\SubscriptionService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends BaseController
{
    public function __construct()
    {
        $this->subscriptionService = new SubscriptionService();
    }

    public function index(Request $request){
        $organizationId = session()->get('current_organization');
        $userId = auth()->user()->id;
        
        // Get current team member info
        $currentTeam = Team::where('organization_id', $organizationId)
            ->where('user_id', $userId)
            ->first();
        
        $isOwner = $currentTeam && $currentTeam->role === 'owner';
        $teamPermissions = $currentTeam ? $currentTeam->getAllPermissions() : [];
        
        $data['subscription'] = Subscription::with('plan')->where('organization_id', $organizationId)->first();
        $data['subscriptionDetails'] = SubscriptionService::calculateSubscriptionBillingDetails($organizationId, $data['subscription']->plan_id);
        $data['subscriptionIsActive'] = SubscriptionService::isSubscriptionActive($organizationId);
        
        // Show counts based on permissions
        // For non-owners, show only their assigned chats count
        if ($isOwner) {
            $data['chatCount'] = in_array('chats', $teamPermissions) || $isOwner ? Chat::where('organization_id', $organizationId)
                ->whereNull('deleted_at')
                ->whereHas('contact', function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->count() : null;
        } else {
            // For agents, show count of contacts assigned to them
            $data['chatCount'] = in_array('chats', $teamPermissions) ? 
                \App\Models\ChatTicket::where('assigned_to', $userId)
                    ->whereHas('contact', function ($query) use ($organizationId) {
                        $query->where('organization_id', $organizationId)->whereNull('deleted_at');
                    })
                    ->count() : null;
        }
        $data['campaignCount'] = in_array('campaigns', $teamPermissions) || $isOwner ? Campaign::where('organization_id', $organizationId)->whereNull('deleted_at')->count() : null;
        $data['contactCount'] = in_array('contacts', $teamPermissions) || $isOwner ? Contact::where('organization_id', $organizationId)->whereNull('deleted_at')->count() : null;
        $data['templateCount'] = in_array('templates', $teamPermissions) || $isOwner ? Template::where('organization_id', $organizationId)->whereNull('deleted_at')->count() : null;
        $data['graphAPIVersion'] = config('graph.api_version');

        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->first();
        $config = $organization->metadata ? json_decode($organization->metadata, true) : [];
        $settings = Setting::whereIn('key', ['is_embedded_signup_active', 'whatsapp_client_id', 'whatsapp_config_id'])
            ->pluck('value', 'key');

        $data['organization'] = $organization;
        $data['campaigns'] = in_array('campaigns', $teamPermissions) || $isOwner ? Campaign::where('organization_id', $organizationId)
            ->whereIn('status', ['pending', 'scheduled'])
            ->limit(5)
            ->get() : [];
        $data['setupWhatsapp'] = isset($config['whatsapp']) ? false : true;
        $data['period'] = $this->period();
        $data['inbound'] = in_array('chats', $teamPermissions) || $isOwner ? $this->getChatCounts('inbound') : [];
        $data['outbound'] = in_array('chats', $teamPermissions) || $isOwner ? $this->getChatCounts('outbound') : [];
        $data['embeddedSignupActive'] = CustomHelper::isModuleEnabled('Embedded Signup');
        $data['appId'] = $settings->get('whatsapp_client_id', null);
        $data['configId'] = $settings->get('whatsapp_config_id', null);
        $data['title'] = __('Dashboard');
        
        // Team member specific data
        $data['isOwner'] = $isOwner;
        $data['teamRole'] = $currentTeam ? $currentTeam->role : null;
        $data['teamPermissions'] = $teamPermissions;
        
        // Agent analytics - chats handled by this agent
        if (!$isOwner && in_array('chats', $teamPermissions)) {
            $data['myChatsCount'] = Chat::where('chats.organization_id', $organizationId)
                ->whereNull('chats.deleted_at')
                ->join('chat_tickets', 'chats.contact_id', '=', 'chat_tickets.contact_id')
                ->where('chat_tickets.assigned_to', $userId)
                ->count();
            $data['myInbound'] = $this->getAgentChatCounts('inbound', $userId);
            $data['myOutbound'] = $this->getAgentChatCounts('outbound', $userId);
        }

        return Inertia::render('User/Dashboard', $data);
    }

    public function dismissNotification(Request $request, $type){
        $currentOrganizationId = session()->get('current_organization');
        $organizationConfig = Organization::where('id', $currentOrganizationId)->first();

        $metadataArray = $organizationConfig->metadata ? json_decode($organizationConfig->metadata, true) : [];

        if($type === 'team'){
            $metadataArray['notification']['team'] = false;
        }

        $updatedMetadataJson = json_encode($metadataArray);

        $organizationConfig->metadata = $updatedMetadataJson;
        $organizationConfig->save();

        return redirect()->route('dashboard')->with(
            'status', [
                'type' => 'success', 
                'message' => __('Notification dismissed successfully!')
            ]
        );
    }

    private function period(){
        $currentDate = Carbon::now();
        $dateArray = [];

        for ($i = 0; $i < 7; $i++) {
            $currentDate->startOfDay();
            $dateArray[] = $currentDate->format('Y-m-d\TH:i:s.000\Z');
            $currentDate->subDay();
        }

        $dateArray = array_reverse($dateArray);

        return $dateArray;
    }

    private function getChatCounts($type){
        $organizationId = session()->get('current_organization');
        $chatCounts = [];

        foreach ($this->period() as $dateString) {
            $date = Carbon::parse($dateString);
            $chatCount = Chat::where('organization_id', $organizationId)
                ->where('type', $type)
                ->whereNull('deleted_at')
                ->whereDate('created_at', $date->toDateString())
                ->count();
            $chatCounts[] = $chatCount;
        }

        return $chatCounts;
    }

    private function getAgentChatCounts($type, $userId){
        $organizationId = session()->get('current_organization');
        $chatCounts = [];

        foreach ($this->period() as $dateString) {
            $date = Carbon::parse($dateString);
            $chatCount = Chat::where('chats.organization_id', $organizationId)
                ->where('chats.type', $type)
                ->whereNull('chats.deleted_at')
                ->join('chat_tickets', 'chats.contact_id', '=', 'chat_tickets.contact_id')
                ->where('chat_tickets.assigned_to', $userId)
                ->whereDate('chats.created_at', $date->toDateString())
                ->count();
            $chatCounts[] = $chatCount;
        }

        return $chatCounts;
    }
}