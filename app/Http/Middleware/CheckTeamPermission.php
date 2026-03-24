<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckTeamPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission = null)
    {
        // Check if the user is logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Only apply to regular users (not admins)
        if ($user->role !== 'user') {
            return $next($request);
        }

        $organizationId = session()->get('current_organization');
        
        if (!$organizationId) {
            return redirect()->route('login');
        }

        $team = Team::where('organization_id', $organizationId)
            ->where('user_id', $user->id)
            ->first();

        if (!$team) {
            return redirect()->route('login');
        }

        // Owner has all permissions
        if ($team->role === 'owner') {
            return $next($request);
        }

        // If a specific permission is required, check it
        if ($permission) {
            if (!$team->hasPermission($permission)) {
                return $this->denyAccess($request);
            }
        } else {
            // Auto-detect permission based on route
            $routePath = $request->path();
            $routePrefix = explode('/', $routePath)[0] ?? '';
            
            if (!$team->hasRouteAccess($routePrefix)) {
                return $this->denyAccess($request);
            }
        }

        return $next($request);
    }

    /**
     * Deny access to the user
     */
    private function denyAccess($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => __('You do not have permission to access this feature.')
            ], 403);
        }

        // Return Inertia page for access denied
        return Inertia::render('User/AccessDenied', [
            'title' => __('Access Denied'),
            'message' => __('You do not have permission to access this feature. Please contact your administrator.')
        ])->toResponse($request)->setStatusCode(403);
    }
}
