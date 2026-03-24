<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function checkPermission(string $permission, $organizationId): void
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        $team = Team::where('user_id', $user->id)
            ->where('organization_id', $organizationId)
            ->first();

        if (! $team || ! $team->hasPermission($permission)) {
            abort(403);
        }
    }
}
