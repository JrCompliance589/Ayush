<?php

namespace App\Http\Controllers;

use App\Models\CountryPricing;
use App\Models\User;
use Illuminate\Http\Request;

class CountryPricingController extends Controller
{
    /**
     * List country pricing records.
     * ?user_id=X returns user-specific pricing merged with global defaults.
     * No user_id returns global pricing only.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $userId = $request->query('user_id');

        if ($userId) {
            // Get global pricing
            $globalQuery = CountryPricing::whereNull('user_id')->orderBy('country_name');
            if ($search) {
                $globalQuery->where(function ($q) use ($search) {
                    $q->where('country_name', 'like', '%' . $search . '%')
                      ->orWhere('country_code', 'like', '%' . $search . '%');
                });
            }
            $globalPricing = $globalQuery->get()->keyBy('country_code');

            // Get user-specific pricing
            $userQuery = CountryPricing::where('user_id', $userId)->orderBy('country_name');
            if ($search) {
                $userQuery->where(function ($q) use ($search) {
                    $q->where('country_name', 'like', '%' . $search . '%')
                      ->orWhere('country_code', 'like', '%' . $search . '%');
                });
            }
            $userPricing = $userQuery->get()->keyBy('country_code');

            // Merge: show user pricing where it exists, global for the rest
            $merged = $globalPricing->map(function ($global) use ($userPricing) {
                if ($userPricing->has($global->country_code)) {
                    $userRow = $userPricing->get($global->country_code);
                    $userRow->is_custom = true;
                    return $userRow;
                }
                $global->is_custom = false;
                return $global;
            });

            // Add any user-specific countries not in global
            foreach ($userPricing as $code => $row) {
                if (!$merged->has($code)) {
                    $row->is_custom = true;
                    $merged->put($code, $row);
                }
            }

            $user = User::find($userId);

            return response()->json([
                'data' => $merged->values(),
                'user' => $user ? ['id' => $user->id, 'first_name' => $user->first_name, 'last_name' => $user->last_name] : null,
            ]);
        }

        // Global pricing (no user_id)
        $query = CountryPricing::whereNull('user_id')->orderBy('country_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('country_name', 'like', '%' . $search . '%')
                  ->orWhere('country_code', 'like', '%' . $search . '%');
            });
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }

    /**
     * Store a new country pricing record (global or per-user).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'country_code' => 'required|string|max:10',
            'country_name' => 'required|string|max:100',
            'marketing_price' => 'required|numeric|min:0',
            'utility_price' => 'required|numeric|min:0',
            'auth_price' => 'required|numeric|min:0',
        ]);

        // Check uniqueness for user_id + country_code
        $exists = CountryPricing::where('country_code', $validated['country_code'])
            ->where(function ($q) use ($validated) {
                if (isset($validated['user_id']) && $validated['user_id']) {
                    $q->where('user_id', $validated['user_id']);
                } else {
                    $q->whereNull('user_id');
                }
            })->exists();

        if ($exists) {
            return response()->json(['message' => 'Pricing for this country code already exists.'], 422);
        }

        $pricing = CountryPricing::create($validated);

        return response()->json([
            'message' => 'Country pricing created successfully',
            'data' => $pricing,
        ]);
    }

    /**
     * Update an existing country pricing record.
     */
    public function update(Request $request, CountryPricing $countryPricing)
    {
        $validated = $request->validate([
            'country_name' => 'sometimes|string|max:100',
            'marketing_price' => 'required|numeric|min:0',
            'utility_price' => 'required|numeric|min:0',
            'auth_price' => 'required|numeric|min:0',
        ]);

        $countryPricing->update($validated);

        return response()->json([
            'message' => 'Country pricing updated successfully',
            'data' => $countryPricing,
        ]);
    }

    /**
     * Save per-user pricing for a country.
     * Creates user-specific override or updates existing one.
     */
    public function saveUserPricing(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'country_code' => 'required|string|max:10',
            'country_name' => 'required|string|max:100',
            'marketing_price' => 'required|numeric|min:0',
            'utility_price' => 'required|numeric|min:0',
            'auth_price' => 'required|numeric|min:0',
        ]);

        $pricing = CountryPricing::updateOrCreate(
            ['user_id' => $validated['user_id'], 'country_code' => $validated['country_code']],
            [
                'country_name' => $validated['country_name'],
                'marketing_price' => $validated['marketing_price'],
                'utility_price' => $validated['utility_price'],
                'auth_price' => $validated['auth_price'],
            ]
        );

        return response()->json([
            'message' => 'User pricing saved successfully',
            'data' => $pricing,
        ]);
    }

    /**
     * Reset a user's custom pricing for a country back to global.
     */
    public function resetUserPricing(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'country_code' => 'required|string|max:10',
        ]);

        CountryPricing::where('user_id', $validated['user_id'])
            ->where('country_code', $validated['country_code'])
            ->delete();

        return response()->json([
            'message' => 'User pricing reset to global default',
        ]);
    }

    /**
     * Delete a country pricing record.
     */
    public function destroy(CountryPricing $countryPricing)
    {
        $countryPricing->delete();

        return response()->json([
            'message' => 'Country pricing deleted successfully',
        ]);
    }
}
