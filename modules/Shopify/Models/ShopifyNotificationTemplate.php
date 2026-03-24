<?php

namespace Modules\Shopify\Models;

use App\Http\Traits\HasUuid;
use App\Models\Template;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopifyNotificationTemplate extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'template_params' => 'array',
        'is_active' => 'boolean',
    ];

    public const EVENT_ORDER_CONFIRMATION = 'order_confirmation';
    public const EVENT_SHIPPING_UPDATE = 'shipping_update';
    public const EVENT_DELIVERY_STATUS = 'delivery_status';
    public const EVENT_COD_VERIFICATION = 'cod_verification';

    public const EVENT_TYPES = [
        self::EVENT_ORDER_CONFIRMATION,
        self::EVENT_SHIPPING_UPDATE,
        self::EVENT_DELIVERY_STATUS,
        self::EVENT_COD_VERIFICATION,
    ];

    public function store()
    {
        return $this->belongsTo(ShopifyStore::class, 'shopify_store_id');
    }

    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }
}
