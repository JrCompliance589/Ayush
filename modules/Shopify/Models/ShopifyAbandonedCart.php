<?php

namespace Modules\Shopify\Models;

use App\Http\Traits\HasUuid;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopifyAbandonedCart extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'line_items' => 'array',
        'total_price' => 'decimal:2',
        'abandoned_at' => 'datetime',
        'recovered_at' => 'datetime',
    ];

    public const STATUS_ABANDONED = 'abandoned';
    public const STATUS_REMINDER_1 = 'reminder_1_sent';
    public const STATUS_REMINDER_2 = 'reminder_2_sent';
    public const STATUS_REMINDER_3 = 'reminder_3_sent';
    public const STATUS_RECOVERED = 'recovered';
    public const STATUS_EXPIRED = 'expired';

    public function shopifyStore()
    {
        return $this->belongsTo(ShopifyStore::class, 'shopify_store_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function scopePendingReminder1($query)
    {
        return $query->where('status', self::STATUS_ABANDONED);
    }

    public function scopePendingReminder2($query)
    {
        return $query->where('status', self::STATUS_REMINDER_1);
    }

    public function scopePendingReminder3($query)
    {
        return $query->where('status', self::STATUS_REMINDER_2);
    }
}
