<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SheetSyncService
{
    private $organizationId;
    private $config;

    public function __construct($organizationId)
    {
        $this->organizationId = $organizationId;
        $this->loadConfig();
    }

    private function loadConfig()
    {
        $organization = Organization::find($this->organizationId);
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];
        $this->config = $metadata['sheet_sync'] ?? [];
    }

    public function isGoogleSheetsEnabled(): bool
    {
        return !empty($this->config['google_sheets']['enabled'])
            && !empty($this->config['google_sheets']['spreadsheet_id'])
            && !empty($this->config['google_sheets']['api_key']);
    }

    public function isZohoSheetEnabled(): bool
    {
        return !empty($this->config['zoho_sheet']['enabled'])
            && !empty($this->config['zoho_sheet']['workbook_id'])
            && !empty($this->config['zoho_sheet']['client_id'])
            && !empty($this->config['zoho_sheet']['client_secret'])
            && !empty($this->config['zoho_sheet']['refresh_token']);
    }

    /**
     * Sync a contact to all enabled sheet providers.
     * Returns array with provider => true/false or error string.
     */
    public function syncContact(array $contactData): array
    {
        $results = [];

        if ($this->isGoogleSheetsEnabled()) {
            $results['google_sheets'] = $this->syncToGoogleSheets($contactData);
        }

        if ($this->isZohoSheetEnabled()) {
            $results['zoho_sheet'] = $this->syncToZohoSheet($contactData);
        }

        return $results;
    }

    /**
     * Append a row to Google Sheets via the Sheets API v4.
     */
    private function syncToGoogleSheets(array $contactData): bool
    {
        try {
            $spreadsheetId = $this->config['google_sheets']['spreadsheet_id'];
            $sheetName = $this->config['google_sheets']['sheet_name'] ?? 'Sheet1';
            $apiKey = $this->config['google_sheets']['api_key'];
            $accessToken = $this->config['google_sheets']['access_token'] ?? null;

            $row = [
                $contactData['first_name'] ?? '',
                $contactData['last_name'] ?? '',
                $contactData['phone'] ?? '',
                $contactData['email'] ?? '',
                $contactData['company_name'] ?? '',
                $contactData['city'] ?? '',
                $contactData['address'] ?? '',
                $contactData['created_at'] ?? now()->toDateTimeString(),
            ];

            $range = urlencode("{$sheetName}!A:H");
            $url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}:append?valueInputOption=USER_ENTERED";

            $headers = ['Content-Type' => 'application/json'];

            if ($accessToken) {
                $headers['Authorization'] = "Bearer {$accessToken}";
            } else {
                $url .= "&key={$apiKey}";
            }

            $response = Http::withHeaders($headers)
                ->timeout(15)
                ->post($url, [
                    'values' => [$row],
                ]);

            if ($response->successful()) {
                Log::info("SheetSync: Google Sheets synced for org {$this->organizationId}", [
                    'phone' => $contactData['phone'] ?? 'N/A',
                ]);
                return true;
            }

            Log::warning("SheetSync: Google Sheets API error for org {$this->organizationId}", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error("SheetSync: Google Sheets exception for org {$this->organizationId}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get a fresh Zoho access token using the refresh token.
     */
    private function getZohoAccessToken(): ?string
    {
        try {
            $clientId = $this->config['zoho_sheet']['client_id'];
            $clientSecret = $this->config['zoho_sheet']['client_secret'];
            $refreshToken = $this->config['zoho_sheet']['refresh_token'];
            $domain = $this->config['zoho_sheet']['zoho_domain'] ?? 'zoho.in';

            $response = Http::asForm()->timeout(15)->post("https://accounts.{$domain}/oauth/v2/token", [
                'refresh_token' => $refreshToken,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'refresh_token',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['access_token'])) {
                    return $data['access_token'];
                }
            }

            Log::warning("SheetSync: Zoho token refresh failed for org {$this->organizationId}", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("SheetSync: Zoho token refresh exception for org {$this->organizationId}", [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Append a row to Zoho Sheet via their API.
     */
    /**
     * Append a row to Zoho Sheet via their API.
     * Returns true on success, or error string on failure.
     */
    private function syncToZohoSheet(array $contactData)
    {
        try {
            $accessToken = $this->getZohoAccessToken();
            if (!$accessToken) {
                Log::error("SheetSync: Could not get Zoho access token for org {$this->organizationId}");
                return 'Could not get Zoho access token. Check your Client ID, Client Secret and Refresh Token.';
            }

            $workbookId = $this->config['zoho_sheet']['workbook_id'];
            $sheetName = $this->config['zoho_sheet']['sheet_name'] ?? 'Sheet1';
            $domain = $this->config['zoho_sheet']['zoho_domain'] ?? 'zoho.in';

            $row = [
                'First Name' => $contactData['first_name'] ?? '',
                'Last Name' => $contactData['last_name'] ?? '',
                'Phone' => $contactData['phone'] ?? '',
                'Email' => $contactData['email'] ?? '',
                'Company Name' => $contactData['company_name'] ?? '',
                'City' => $contactData['city'] ?? '',
                'Address' => $contactData['address'] ?? '',
                'Created At' => $contactData['created_at'] ?? now()->toDateTimeString(),
            ];

            $url = "https://sheet.{$domain}/api/v2/{$workbookId}";

            $response = Http::withHeaders([
                'Authorization' => "Zoho-oauthtoken {$accessToken}",
            ])
                ->timeout(15)
                ->asForm()
                ->post($url, [
                    'method' => 'worksheet.records.add',
                    'worksheet_name' => $sheetName,
                    'header_row' => 1,
                    'json_data' => json_encode([$row]),
                ]);

            $responseData = $response->json();
            $errorCode = $responseData['error_code'] ?? null;

            // No header row in the sheet
            if ($errorCode == 2884) {
                Log::warning("SheetSync: Zoho Sheet missing headers for org {$this->organizationId}");
                return 'MISSING_HEADERS';
            }

            // Sheet does not exist
            if ($errorCode == 2863) {
                return 'Sheet "' . $sheetName . '" not found. Check your Sheet Name.';
            }

            if ($response->successful() && $errorCode === null) {
                Log::info("SheetSync: Zoho Sheet synced for org {$this->organizationId}", [
                    'phone' => $contactData['phone'] ?? 'N/A',
                ]);
                return true;
            }

            Log::warning("SheetSync: Zoho Sheet API error for org {$this->organizationId}", [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $url,
            ]);
            return $responseData['error_message'] ?? 'Unknown Zoho API error.';
        } catch (\Exception $e) {
            Log::error("SheetSync: Zoho Sheet exception for org {$this->organizationId}", [
                'error' => $e->getMessage(),
            ]);
            return 'Exception: ' . $e->getMessage();
        }
    }
}
