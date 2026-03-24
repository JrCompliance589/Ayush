<?php

namespace Modules\Shopify\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Shopify\Services\ShopifyCartRecoveryService;

class SendCartReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    protected int $cartId;
    protected int $reminderNumber;

    public function __construct(int $cartId, int $reminderNumber)
    {
        $this->cartId = $cartId;
        $this->reminderNumber = $reminderNumber;
    }

    public function handle(ShopifyCartRecoveryService $cartRecoveryService): void
    {
        $cartRecoveryService->processReminder($this->cartId, $this->reminderNumber);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Shopify: Cart reminder job failed for cart {$this->cartId}, reminder {$this->reminderNumber}: " . $exception->getMessage());
    }
}
