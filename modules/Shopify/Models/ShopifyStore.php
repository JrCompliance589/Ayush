<?php

namespace Modules\Shopify\Models;

use App\Http\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopifyStore extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'enabled_events' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'api_secret',
        'access_token',
        'webhook_secret',
    ];

    public function notificationTemplates()
    {
        return $this->hasMany(ShopifyNotificationTemplate::class, 'shopify_store_id');
    }

    public function abandonedCarts()
    {
        return $this->hasMany(ShopifyAbandonedCart::class, 'shopify_store_id');
    }

    public function cartRecoverySettings()
    {
        return $this->hasOne(ShopifyCartRecoverySetting::class, 'shopify_store_id');
    }

    public function notificationLogs()
    {
        return $this->hasMany(ShopifyNotificationLog::class, 'shopify_store_id');
    }
}
