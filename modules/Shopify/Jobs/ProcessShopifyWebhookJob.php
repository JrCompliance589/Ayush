<?php

namespace Modules\Shopify\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Shopify\Services\ShopifyWebhookService;
use Modules\Shopify\Models\ShopifyStore;

class ProcessShopifyWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    protected int $storeId;
    protected string $topic;
    protected array $payload;

    public function __construct(int $storeId, string $topic, array $payload)
    {
        $this->storeId = $storeId;
        $this->topic = $topic;
        $this->payload = $payload;
    }

    public function handle(ShopifyWebhookService $webhookService): void
    {
        Log::channel('shopify')->info('Job started: ProcessShopifyWebhookJob', [
            'store_id' => $this->storeId,
            'topic' => $this->topic,
        ]);

        $store = ShopifyStore::find($this->storeId);
        if (!$store || !$store->is_active) {
            Log::channel('shopify')->warning('Store not found or inactive in job', ['store_id' => $this->storeId]);
            return;
        }

        $webhookService->processWebhook($store, $this->topic, $this->payload);

        Log::channel('shopify')->info('Job completed: ProcessShopifyWebhookJob', [
            'store_id' => $this->storeId,
            'topic' => $this->topic,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('shopify')->error('Job FAILED: ProcessShopifyWebhookJob', [
            'store_id' => $this->storeId,
            'topic' => $this->topic,
            'error' => $exception->getMessage(),
        ]);
        Log::error("Shopify: Webhook job failed for store {$this->storeId}, topic {$this->topic}: " . $exception->getMessage());
    }
}
