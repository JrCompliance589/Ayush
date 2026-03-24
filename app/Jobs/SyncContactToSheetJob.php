<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Services\SheetSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncContactToSheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    private int $organizationId;
    private ?int $contactId;
    private array $contactData;

    /**
     * Accept either contactId (preferred, fetches fresh data) or contactData array (for test).
     */
    public function __construct(int $organizationId, $contactIdOrData)
    {
        $this->organizationId = $organizationId;

        if (is_array($contactIdOrData)) {
            // Direct data (used by test connection)
            $this->contactId = null;
            $this->contactData = $contactIdOrData;
        } else {
            // Contact ID (used by automation/webhook — fetch fresh data at runtime)
            $this->contactId = (int) $contactIdOrData;
            $this->contactData = [];
        }
    }

    public function handle(): void
    {
        try {
            // If contactId provided, check dedup cache and fetch fresh data from DB
            if ($this->contactId) {
                $cacheKey = "sheet_sync_done_{$this->organizationId}_{$this->contactId}";

                // Skip if another delayed job already synced this contact recently
                if (Cache::get($cacheKey)) {
                    Log::info("SyncContactToSheetJob: skipped (already synced recently) contact {$this->contactId}");
                    return;
                }

                $contact = Contact::find($this->contactId);
                if (!$contact) {
                    Log::warning("SyncContactToSheetJob: contact {$this->contactId} not found");
                    return;
                }

                $address = is_string($contact->address) ? json_decode($contact->address, true) : ($contact->address ?? []);
                $metadata = is_string($contact->metadata) ? json_decode($contact->metadata, true) : ($contact->metadata ?? []);

                // Find company name from metadata (could be stored with various key formats)
                $companyName = '';
                foreach ($metadata as $key => $value) {
                    $normalizedKey = strtolower(str_replace(['metadata.', ' ', '_', '-'], '', $key));
                    if ($normalizedKey === 'companyname') {
                        $companyName = $value ?? '';
                        break;
                    }
                }

                $this->contactData = [
                    'first_name' => $contact->first_name ?? '',
                    'last_name' => $contact->last_name ?? '',
                    'phone' => $contact->phone ?? '',
                    'email' => $contact->email ?? '',
                    'company_name' => $companyName,
                    'city' => $address['city'] ?? '',
                    'address' => trim(implode(', ', array_filter([
                        $address['street'] ?? '',
                        $address['city'] ?? '',
                        $address['state'] ?? '',
                        $address['zip'] ?? '',
                        $address['country'] ?? '',
                    ])), ', '),
                    'created_at' => $contact->created_at instanceof \DateTimeInterface
                        ? $contact->created_at->toDateTimeString()
                        : ($contact->created_at ?? now()->toDateTimeString()),
                ];

                // Mark as synced for 2 minutes (prevents duplicate rows from rapid updates)
                Cache::put($cacheKey, true, 120);
            }

            $service = new SheetSyncService($this->organizationId);
            $results = $service->syncContact($this->contactData);

            Log::info("SyncContactToSheetJob: completed for org {$this->organizationId}", $results);
        } catch (\Exception $e) {
            Log::error("SyncContactToSheetJob: failed for org {$this->organizationId}", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
