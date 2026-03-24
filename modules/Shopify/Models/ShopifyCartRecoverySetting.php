<?php

namespace Modules\Shopify\Models;

use App\Http\Traits\HasUuid;
use App\Models\Template;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopifyCartRecoverySetting extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'reminder_1_enabled' => 'boolean',
        'reminder_2_enabled' => 'boolean',
        'reminder_3_enabled' => 'boolean',
        'reminder_1_params' => 'array',
        'reminder_2_params' => 'array',
        'reminder_3_params' => 'array',
        'discount_percentage' => 'decimal:2',
    ];

    public function shopifyStore()
    {
        return $this->belongsTo(ShopifyStore::class, 'shopify_store_id');
    }

    public function reminder1Template()
    {
        return $this->belongsTo(Template::class, 'reminder_1_template_id');
    }

    public function reminder2Template()
    {
        return $this->belongsTo(Template::class, 'reminder_2_template_id');
    }

    public function reminder3Template()
    {
        return $this->belongsTo(Template::class, 'reminder_3_template_id');
    }
}
