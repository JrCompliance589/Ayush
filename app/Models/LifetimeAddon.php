<?php

namespace App\Models;

use App\Http\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LifetimeAddon extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $guarded = [];
    public $timestamps = true;

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Get all addons with optional search.
     */
    public function listAll($searchTerm = null)
    {
        return $this->where('deleted_at', null)
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            })
            ->latest()
            ->paginate(10);
    }

    /**
     * Scope for active addons only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope by addon type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get user purchases for this addon.
     */
    public function purchases()
    {
        return $this->hasMany(UserLifetimeAddon::class, 'addon_id');
    }

    /**
     * Check if addon is for campaigns.
     */
    public function isCampaignAddon(): bool
    {
        return $this->type === 'campaign_limit';
    }

    /**
     * Check if addon is for contacts.
     */
    public function isContactsAddon(): bool
    {
        return $this->type === 'contacts_limit';
    }
}
