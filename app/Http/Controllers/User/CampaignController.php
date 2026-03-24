<?php

namespace App\Http\Controllers\User;

use App\Exports\CampaignDetailsExport;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\StoreCampaign;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CampaignLogResource;
use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Organization;
use App\Models\Template;
use App\Models\BalanceHistory;
use App\Services\CampaignService;
use App\Services\LifetimeSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class CampaignController extends BaseController
{
    private $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function index(Request $request, $uuid = null){
        \Log::info("CampaignController::index called with uuid: " . ($uuid ?? 'null'));
        $organizationId = session()->get('current_organization');
        \Log::info("Organization ID: {$organizationId}");
        if ($uuid == null) {
            $searchTerm = $request->query('search');
            \Log::info("Fetching campaigns list with search term: " . ($searchTerm ?? 'none'));
            $settings = Organization::where('id', $organizationId)->first();
            $rows = CampaignResource::collection(
                Campaign::with(['template', 'campaignLogs'])
                    ->where('organization_id', $organizationId)
                    ->where('deleted_at', null)
                    ->where(function ($query) use ($searchTerm) {
                        $query->where('name', 'like', '%' . $searchTerm . '%')
                              ->orWhereHas('template', function ($templateQuery) use ($searchTerm) {
                                  $templateQuery->where('name', 'like', '%' . $searchTerm . '%');
                              });
                    })
                    ->latest()
                    ->paginate(10)
            );

            return Inertia::render('User/Campaign/Index', [
                'title'=> __('Campaigns'),
                'allowCreate' => true,
                'rows' => $rows,
                'filters' => request()->all(['search']),
                'settings' => $settings
            ]);
        } else if ($uuid == 'create') {
            \Log::info("Loading campaign creation form for organization: {$organizationId}");
            $data['settings'] = Organization::where('id', $organizationId)->first();
            $data['templates'] = Template::where('organization_id', $organizationId)
                ->where('deleted_at', null)
                ->where('status', 'APPROVED')
                ->get();
            \Log::info("Loaded " . count($data['templates']) . " templates for organization: {$organizationId}");

            $data['contactGroups'] = ContactGroup::where('organization_id', $organizationId)
                ->where('deleted_at', null)
                ->get();
            \Log::info("Loaded " . count($data['contactGroups']) . " contact groups for organization: {$organizationId}");

            $data['title'] = __('Create campaign');

            return Inertia::render('User/Campaign/Create', $data);
        } else {
            \Log::info("Loading campaign details for UUID: {$uuid}");
            $data['campaign'] = Campaign::with('contactGroup', 'template')->where('uuid', $uuid)->first();

            if ($data['campaign']) {
                \Log::info("Campaign found: {$data['campaign']->id} - {$data['campaign']->name}");
                $counts = $data['campaign']->getCounts();
                \Log::info("Campaign counts - Messages: {$counts->total_message_count}, Sent: {$counts->total_sent_count}, Delivered: {$counts->total_delivered_count}, Failed: {$counts->total_failed_count}, Read: {$counts->total_read_count}");
                $data['campaign']['total_message_count'] = $counts->total_message_count ?? 0;
                $data['campaign']['total_sent_count'] = $counts->total_sent_count ?? 0;
                $data['campaign']['total_delivered_count'] = $counts->total_delivered_count ?? 0;
                $data['campaign']['total_failed_count'] = $counts->total_failed_count ?? 0;
                $data['campaign']['total_read_count'] = $counts->total_read_count ?? 0;
            } else {
                \Log::warning("Campaign not found for UUID: {$uuid}");
                $data['campaign']['total_message_count'] = 0;
                $data['campaign']['total_sent_count'] = 0;
                $data['campaign']['total_delivered_count'] = 0;
                $data['campaign']['total_failed_count'] = 0;
                $data['campaign']['total_read_count'] = 0;
            }

            $data['filters'] = request()->all(['search']);

            $searchTerm = $request->query('search');
            \Log::info("Fetching campaign logs for campaign {$data['campaign']->id} with search: " . ($searchTerm ?? 'none'));
            $data['rows'] = CampaignLogResource::collection(
                CampaignLog::with('contact', 'chat.logs', 'retries')
                    ->where('campaign_id', $data['campaign']->id)
                    ->where(function ($query) use ($searchTerm) {
                        $query->whereHas('contact', function ($contactQuery) use ($searchTerm) {
                            $contactQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                                         ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                                         ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                        });
                    })
                    ->orderBy('id')
                    ->paginate(10)
            );
            $data['title'] = __('View campaign');

            return Inertia::render('User/Campaign/View', $data);
        }
    }

    public function store(StoreCampaign $request){
       
        $templateUuid = $request->input('template');
        \Log::info("Template UUID: " . ($templateUuid ?? 'none'));
        $templateCategory = null;

        if ($templateUuid) {
            $template = \App\Models\Template::where('uuid', $templateUuid)->first();
            $templateCategory = $template ? $template->category : null;
            \Log::info("Template found: Category = {$templateCategory}");
        }

        $contactsGroupUuid = $request->input('contacts');
        $contactsCount = null;
        \Log::info("Contacts group UUID: " . ($contactsGroupUuid ?? 'none'));

        if ($contactsGroupUuid) {
            if ($contactsGroupUuid === 'all') {
                // Get all contacts for the organization
                $organizationId = session()->get('current_organization');
                $contactsCount = \App\Models\Contact::where('organization_id', $organizationId)->count();
                \Log::info("Fetching all contacts for organization. Contact count: {$contactsCount}");
            } else {
                $group = \App\Models\ContactGroup::where('uuid', $contactsGroupUuid)->first();
                $contactsCount = $group ? $group->contacts()->count() : 0;
                \Log::info("Contact group found with {$contactsCount} contacts");
            }
        }

        $userId = method_exists($request, 'user') && $request->user()
                    ? $request->user()->id
                    : auth()->id();
        \Log::info("User ID: {$userId}");

        $user = $userId ? \App\Models\User::find($userId) : null;
        \Log::info("User found: " . ($user ? "Balance = {$user->balance}" : "User not found"));

        if (!$user || $user->balance < 1) {
            \Log::warning("Insufficient balance or user not found for campaign creation");
            return Redirect::route('campaigns')->with('status', [
                'type' => 'error',
                'message' => __('Insufficient balance to create campaign.')
            ]);
        }

        $marketingPrice = (float) ($user->marketing_price ?? 0);
        $utilityPrice   = (float) ($user->utility_price ?? 0);
        $authPrice      = (float) ($user->auth_price ?? 0);
        $contactsCount  = (int) ($contactsCount ?? 0);
        \Log::info("Pricing - Marketing: {$marketingPrice}, Utility: {$utilityPrice}, Auth: {$authPrice}, Contacts: {$contactsCount}");

        $category = strtolower((string) $templateCategory);
        \Log::info("Template category: {$category}");
        switch ($category) {
            case 'marketing': $perContactPrice = $marketingPrice; break;
            case 'utility':   $perContactPrice = $utilityPrice;   break;
            case 'auth':      $perContactPrice = $authPrice;      break;
            default:          $perContactPrice = $marketingPrice; break;
        }

        $calculatedCharge = $perContactPrice * $contactsCount;
        $charge = round($calculatedCharge, 2);
        \Log::info("Calculated charge: {$charge} ({$perContactPrice} x {$contactsCount})");

        $oldBalance = (float) $user->balance;
        $newBalance = round($oldBalance - $charge, 2);
        \Log::info("Balance - Old: {$oldBalance}, Charge: {$charge}, New: {$newBalance}");
        if ($user->balance < $charge) {
            \Log::warning("Insufficient balance for campaign. Required: {$charge}, Available: {$user->balance}");
            return Redirect::route('campaigns')->with('status', [
                'type' => 'error',
                'message' => __('Insufficient balance to create this campaign.')
            ]);
        }
        $user->balance = $newBalance;
        $user->save();
        \Log::info("User balance updated from {$oldBalance} to {$newBalance}");

        BalanceHistory::create([
            'user_id' => $userId,
            'amount' => -$charge,
            'balance_after' => $newBalance,
            'type' => 'debit',
            'note' => "Campaign charge for {$contactsCount} contacts"
        ]);
        \Log::info("Balance history record created for campaign charge: -{$charge}");

        // Record daily usage for lifetime subscribers
        $organizationId = session()->get('current_organization');
        if (LifetimeSubscriptionService::hasLifetimeSubscription($organizationId)) {
            LifetimeSubscriptionService::recordCampaignCreation($organizationId);
            \Log::info("Recorded lifetime subscription daily usage: 1 campaign");
        }

        \Log::info("Calling campaign service to store campaign");
        $this->campaignService->store($request);
        \Log::info("Campaign stored successfully");

        return Redirect::route('campaigns')->with(
            'status', [
                'type' => 'success', 
                'message' => __('Campaign created successfully!')
            ]
        );
    }

    public function export($uuid = null){
        \Log::info("Exporting campaign with UUID: " . ($uuid ?? 'all'));
        return Excel::download(new CampaignDetailsExport($uuid), 'campaign.csv');
    }

    public function delete($uuid){
        \Log::info("Deleting campaign with UUID: {$uuid}");
        $this->campaignService->destroy($uuid);
        \Log::info("Campaign deleted successfully: {$uuid}");

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('Row deleted successfully!')
            ]
        );
    }

    public function storeCarousel(Request $request)
    {
        \Log::info("CampaignController::storeCarousel called");
        
        $organizationId = session()->get('current_organization');
        
        // Check daily limit for lifetime subscribers
        if (LifetimeSubscriptionService::hasLifetimeSubscription($organizationId)) {
            if (!LifetimeSubscriptionService::canCreateCampaign($organizationId)) {
                return Redirect::route('campaigns')->with(
                    'status',
                    [
                        'type' => 'error',
                        'message' => __('You have reached your daily campaign limit. Purchase addons to increase your limit or wait until tomorrow.')
                    ]
                );
            }
        }
        
        // Get contacts count for usage tracking
        $contactsGroupUuid = $request->input('contacts');
        $contactsCount = 0;
        if ($contactsGroupUuid) {
            if ($contactsGroupUuid === 'all') {
                $contactsCount = \App\Models\Contact::where('organization_id', $organizationId)->count();
            } else {
                $group = \App\Models\ContactGroup::where('uuid', $contactsGroupUuid)->first();
                $contactsCount = $group ? $group->contacts()->count() : 0;
            }
        }
        
        $this->campaignService->storeCarousel($request);
        \Log::info("Carousel campaign stored successfully");
        
        // Record daily usage for lifetime subscribers
        if (LifetimeSubscriptionService::hasLifetimeSubscription($organizationId)) {
            LifetimeSubscriptionService::recordCampaignCreation($organizationId);
            \Log::info("Recorded lifetime subscription daily usage: 1 campaign");
        }

        return Redirect::route('campaigns')->with(
            'status',
            [
                'type' => 'success',
                'message' => __('Campaign created successfully!')
            ]
        );
    }
}
