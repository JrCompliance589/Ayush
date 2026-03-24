<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class SheetSyncController extends BaseController
{
    public function index()
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::findOrFail($organizationId);
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        $data['title'] = __('Sheet Sync');
        $data['settings'] = $organization;
        $data['sheetSyncConfig'] = $metadata['sheet_sync'] ?? [
            'google_sheets' => [
                'enabled' => false,
                'spreadsheet_id' => '',
                'sheet_name' => 'Sheet1',
                'api_key' => '',
                'access_token' => '',
            ],
            'zoho_sheet' => [
                'enabled' => false,
                'client_id' => '',
                'client_secret' => '',
                'refresh_token' => '',
                'workbook_id' => '',
                'sheet_name' => 'Sheet1',
                'zoho_domain' => 'zoho.in',
            ],
        ];
        $data['modules'] = \App\Models\Addon::get();

        return Inertia::render('User/Settings/SheetSync', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'google_sheets.enabled' => 'required|boolean',
            'google_sheets.spreadsheet_id' => 'nullable|string|max:255',
            'google_sheets.sheet_name' => 'nullable|string|max:255',
            'google_sheets.api_key' => 'nullable|string|max:500',
            'google_sheets.access_token' => 'nullable|string|max:2000',
            'zoho_sheet.enabled' => 'required|boolean',
            'zoho_sheet.client_id' => 'nullable|string|max:500',
            'zoho_sheet.client_secret' => 'nullable|string|max:500',
            'zoho_sheet.refresh_token' => 'nullable|string|max:2000',
            'zoho_sheet.workbook_id' => 'nullable|string|max:255',
            'zoho_sheet.sheet_name' => 'nullable|string|max:255',
            'zoho_sheet.zoho_domain' => 'nullable|string|max:50',
        ]);

        $organizationId = session()->get('current_organization');
        $organization = Organization::findOrFail($organizationId);
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        $metadata['sheet_sync'] = [
            'google_sheets' => [
                'enabled' => (bool) $request->input('google_sheets.enabled'),
                'spreadsheet_id' => $request->input('google_sheets.spreadsheet_id', ''),
                'sheet_name' => $request->input('google_sheets.sheet_name', 'Sheet1'),
                'api_key' => $request->input('google_sheets.api_key', ''),
                'access_token' => $request->input('google_sheets.access_token', ''),
            ],
            'zoho_sheet' => [
                'enabled' => (bool) $request->input('zoho_sheet.enabled'),
                'client_id' => $request->input('zoho_sheet.client_id', ''),
                'client_secret' => $request->input('zoho_sheet.client_secret', ''),
                'refresh_token' => $request->input('zoho_sheet.refresh_token', ''),
                'workbook_id' => $request->input('zoho_sheet.workbook_id', ''),
                'sheet_name' => $request->input('zoho_sheet.sheet_name', 'Sheet1'),
                'zoho_domain' => $request->input('zoho_sheet.zoho_domain', 'zoho.in'),
            ],
        ];

        $organization->metadata = json_encode($metadata);

        if ($organization->save()) {
            return Redirect::back()->with('status', [
                'type' => 'success',
                'message' => __('Sheet sync settings updated successfully!'),
            ]);
        }

        return Redirect::back()->with('status', [
            'type' => 'error',
            'message' => __('Something went wrong. Please try again.'),
        ]);
    }

    public function test(Request $request)
    {
        $organizationId = session()->get('current_organization');

        $provider = $request->input('provider'); // 'google_sheets' or 'zoho_sheet'

        $service = new \App\Services\SheetSyncService($organizationId);

        $testContact = [
            'first_name' => 'Test',
            'last_name' => 'Contact',
            'phone' => '+1234567890',
            'email' => 'test@example.com',
            'company_name' => 'Test Company',
            'city' => 'Test City',
            'address' => '123 Test Street, Test City, TS 12345',
            'created_at' => now()->toDateTimeString(),
        ];

        if ($provider === 'google_sheets' && $service->isGoogleSheetsEnabled()) {
            $result = $service->syncContact($testContact);
            $success = $result['google_sheets'] ?? false;
        } elseif ($provider === 'zoho_sheet' && $service->isZohoSheetEnabled()) {
            $result = $service->syncContact($testContact);
            $success = $result['zoho_sheet'] ?? false;
        } else {
            return response()->json([
                'success' => false,
                'message' => __('Provider not enabled. Please save settings first.'),
            ]);
        }

        if ($provider === 'zoho_sheet') {
            $zohoResult = $result['zoho_sheet'] ?? false;
            if ($zohoResult === true) {
                return response()->json(['success' => true, 'message' => __('Test row added to Zoho Sheet successfully!')]);
            }
            if ($zohoResult === 'MISSING_HEADERS') {
                return response()->json([
                    'success' => false,
                    'message' => __('Your Zoho Sheet is missing header row. Please add these headers in Row 1: First Name, Last Name, Phone, Email, Created At — then try again.'),
                ]);
            }
            return response()->json(['success' => false, 'message' => is_string($zohoResult) ? $zohoResult : __('Failed to add test row.')]);
        }

        return response()->json([
            'success' => $success,
            'message' => $success
                ? __('Test row added to sheet successfully!')
                : __('Failed to add test row. Check your credentials and sheet ID.'),
        ]);
    }

    /**
     * Exchange Zoho authorization code for refresh token.
     */
    public function zohoGenerateToken(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'zoho_domain' => 'required|string',
        ]);

        $domain = $request->input('zoho_domain', 'zoho.in');

        $response = \Illuminate\Support\Facades\Http::asForm()->timeout(15)->post("https://accounts.{$domain}/oauth/v2/token", [
            'code' => $request->input('code'),
            'client_id' => $request->input('client_id'),
            'client_secret' => $request->input('client_secret'),
            'grant_type' => 'authorization_code',
            'redirect_uri' => url('/settings/sheet-sync/zoho/callback'),
        ]);

        $data = $response->json();

        if ($response->successful() && !empty($data['refresh_token'])) {
            return response()->json([
                'success' => true,
                'refresh_token' => $data['refresh_token'],
                'access_token' => $data['access_token'] ?? null,
                'message' => __('Refresh token generated successfully!'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $data['error'] ?? __('Failed to generate token. Please try again.'),
        ], 400);
    }

    /**
     * Handle Zoho OAuth callback redirect.
     */
    public function zohoCallback(Request $request)
    {
        $code = $request->query('code');

        return Inertia::render('User/Settings/SheetSync', [
            'zohoAuthCode' => $code,
            'title' => __('Sheet Sync'),
            'settings' => Organization::findOrFail(session()->get('current_organization')),
            'sheetSyncConfig' => json_decode(Organization::findOrFail(session()->get('current_organization'))->metadata ?? '{}', true)['sheet_sync'] ?? [],
            'modules' => \App\Models\Addon::get(),
        ]);
    }
}
