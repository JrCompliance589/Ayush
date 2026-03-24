<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\SubscriptionHelper;
use App\Models\Team;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CheckClientRole
{
    public function handle($request, Closure $next)
    {
        // Check if the user is logged in
        if (Auth::check()) {
            $user = Auth::user();

            // Check if the user role is 'user'
            if ($user->role === 'user') {
                $organizationId = session()->get('current_organization');
                $team = Team::where('organization_id', $organizationId)->where('user_id', auth()->user()->id)->first();

                if($team && ($team->role === 'manager' || $team->role === 'agent')){
                    // Check if the agent/manager has permission for the current route
                    $routePath = $request->path();
                    $routePrefix = explode('/', $routePath)[0] ?? '';

                    // Allow access if team member has permission for this route
                    if ($team->hasRouteAccess($routePrefix)) {
                        return $next($request);
                    }

                    return to_route('dashboard');
                }
            }
        }

        // Subscription is active or user role is not 'user', proceed to the next page
        return $next($request);
    }
}
