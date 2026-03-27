<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryPricing extends Model
{
    use HasFactory;

    protected $table = 'country_pricing';

    protected $fillable = [
        'user_id',
        'country_code',
        'country_name',
        'marketing_price',
        'utility_price',
        'auth_price',
    ];

    protected $casts = [
        'marketing_price' => 'decimal:4',
        'utility_price' => 'decimal:4',
        'auth_price' => 'decimal:4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get pricing for a given country code and user.
     * First checks user-specific pricing, then falls back to global (user_id=null).
     */
    public static function getPriceForCountryCode(string $countryCode, ?int $userId = null): ?self
    {
        if ($userId) {
            $userPricing = static::where('country_code', $countryCode)
                ->where('user_id', $userId)
                ->first();
            if ($userPricing) {
                return $userPricing;
            }
        }

        return static::where('country_code', $countryCode)
            ->whereNull('user_id')
            ->first();
    }
}
