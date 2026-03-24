<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Team;
use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionExpiryController extends Controller
{
    /**
     * Get subscription info for a user
     */
    public function getSubscription($userId)
    {
        $user = User::findOrFail($userId);
        
        // Get the user's organization through team
        $team = Team::where('user_id', $userId)->first();
        
        if (!$team) {
            return response()->json([
                'has_subscription' => false,
                'message' => 'User has no organization'
            ]);
        }

        $subscription = Subscription::with('plan')
            ->where('organization_id', $team->organization_id)
            ->first();

        if (!$subscription) {
            return response()->json([
                'has_subscription' => false,
                'message' => 'No subscription found'
            ]);
        }

        return response()->json([
            'has_subscription' => true,
            'subscription' => [
                'id' => $subscription->id,
                'plan_name' => $subscription->plan?->name ?? 'N/A',
                'status' => $subscription->status,
                'valid_until' => $subscription->valid_until ? Carbon::parse($subscription->valid_until)->format('Y-m-d') : null,
                'organization_id' => $team->organization_id
            ]
        ]);
    }

    /**
     * Update subscription expiry date
     */
    public function updateExpiry(Request $request, $userId)
    {
        $request->validate([
            'valid_until' => 'required|date',
        ]);

        $user = User::findOrFail($userId);
        
        // Get the user's organization through team
        $team = Team::where('user_id', $userId)->first();
        
        if (!$team) {
            return response()->json([
                'message' => 'User has no organization'
            ], 404);
        }

        $subscription = Subscription::where('organization_id', $team->organization_id)->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'No subscription found'
            ], 404);
        }

        $subscription->valid_until = Carbon::parse($request->valid_until)->endOfDay();
        $subscription->save();

        return response()->json([
            'message' => 'Subscription expiry updated successfully',
            'subscription' => $subscription
        ]);
    }
}
