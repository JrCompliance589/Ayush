<?php

namespace App\Models;

use App\Http\Traits\HasUuid;
use App\Helpers\DateTimeHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLifetimeAddon extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];
    public $timestamps = true;

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price_paid' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function getCreatedAtAttribute($value)
    {
        return DateTimeHelper::convertToOrganizationTimezone($value)->toDateTimeString();
    }

    /**
     * Get the organization that owns this addon purchase.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Get the addon that was purchased.
     */
    public function addon()
    {
        return $this->belongsTo(LifetimeAddon::class, 'addon_id');
    }

    /**
     * Scope for completed purchases.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending purchases.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get the total quantity purchased for this addon type.
     * The quantity stored is already the total campaigns/contacts to add.
     */
    public static function getTotalQuantityByType($organizationId, $type)
    {
        return self::where('organization_id', $organizationId)
            ->where('status', 'completed')
            ->whereHas('addon', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->sum('quantity');
    }
}
