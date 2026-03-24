<?php

namespace App\Http\Controllers\User;

use DB;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\StoreTeam;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Models\TeamInvite;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TeamController extends BaseController
{
    private $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index(Request $request){
        $rows = TeamResource::collection(
            Team::with('user')->where('organization_id', session()->get('current_organization'))
                ->latest()->paginate(10)
        );

        // Get available permissions from config
        $availablePermissions = config('team_permissions.permissions', []);

        // Get pending invites for this organization
        $pendingInvites = TeamInvite::where('organization_id', session()->get('current_organization'))
            ->select('id', 'email', 'role', 'code', 'expire_at', 'invited_by')
            ->get()
            ->map(function ($invite) {
                return [
                    'id' => $invite->id,
                    'email' => $invite->email,
                    'role' => $invite->role,
                    'invite_link' => url('/invite/' . $invite->code),
                    'expire_at' => $invite->expire_at,
                    'is_expired' => $invite->expire_at < now(),
                ];
            });

        if($request->expectsJson()){
            $rows = DB::table('users')
                ->join('teams', 'users.id', '=', 'teams.user_id')
                ->where('teams.organization_id', '=', session()->get('current_organization'))
                ->whereNull('teams.deleted_at')
                ->select('users.*')
                ->get();

            return response()->json([
                'rows' => $rows
            ]);
        } else {
            return Inertia::render('User/Team/Index', [
                'title' => __('Team'),
                'filters' => $request->all(),
                'rows' => $rows,
                'availablePermissions' => $availablePermissions,
                'pendingInvites' => $pendingInvites,
            ]);
        }
    }

    public function invite(StoreTeam $request){
        $this->teamService->invite($request);

        //response()->json(['success' => true, 'message'=> __('User invited successfully!'), 'data' => $invite])

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('User invited successfully!')
            ]
        );
    }

    public function update(Request $request, $uuid){
        $this->teamService->update($request, $uuid);

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('User account updated successfully!')
            ]
        );
    }

    public function delete($uuid)
    {
        $this->teamService->destroy($uuid);
    }

    /**
     * Update team member permissions
     */
    public function updatePermissions(Request $request, $uuid)
    {
        // Validate that current user is the owner
        $currentTeam = Team::where('organization_id', session()->get('current_organization'))
            ->where('user_id', auth()->user()->id)
            ->first();

        if (!$currentTeam || $currentTeam->role !== 'owner') {
            return Redirect::back()->with(
                'status', [
                    'type' => 'error',
                    'message' => __('Only organization owner can manage permissions!')
                ]
            );
        }

        // Find the team member to update
        $team = Team::where('uuid', $uuid)
            ->where('organization_id', session()->get('current_organization'))
            ->first();

        if (!$team) {
            return Redirect::back()->with(
                'status', [
                    'type' => 'error',
                    'message' => __('Team member not found!')
                ]
            );
        }

        // Cannot change owner permissions
        if ($team->role === 'owner') {
            return Redirect::back()->with(
                'status', [
                    'type' => 'error',
                    'message' => __('Cannot modify owner permissions!')
                ]
            );
        }

        // Validate permissions
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string',
        ]);

        // Get valid permission keys
        $validPermissions = array_column(config('team_permissions.permissions', []), 'key');
        $permissions = array_filter($request->permissions, function($perm) use ($validPermissions) {
            return in_array($perm, $validPermissions);
        });

        // Update permissions
        $team->setPermissions(array_values($permissions));

        return Redirect::back()->with(
            'status', [
                'type' => 'success',
                'message' => __('Permissions updated successfully!')
            ]
        );
    }

    /**
     * Get team member permissions
     */
    public function getPermissions($uuid)
    {
        $team = Team::where('uuid', $uuid)
            ->where('organization_id', session()->get('current_organization'))
            ->first();

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => __('Team member not found!')
            ], 404);
        }

        return response()->json([
            'success' => true,
            'permissions' => $team->getAllPermissions(),
            'role' => $team->role,
        ]);
    }

    /**
     * Resend invitation email and regenerate link if expired
     */
    public function resendInvite($id)
    {
        $invite = TeamInvite::where('id', $id)
            ->where('organization_id', session()->get('current_organization'))
            ->first();

        if (!$invite) {
            return Redirect::back()->with(
                'status', [
                    'type' => 'error',
                    'message' => __('Invitation not found!')
                ]
            );
        }

        // Regenerate code if expired
        if ($invite->expire_at < now()) {
            $invite->code = md5(now()->timestamp . session()->get('current_organization') . \Str::random(32));
            $invite->expire_at = now()->addDay();
            $invite->save();
        }

        // Resend email
        $inviter = \App\Models\User::find(auth()->user()->id);
        \App\Helpers\Email::sendInvite('Invite', $invite->email, $inviter, url('/invite/' . $invite->code));

        return Redirect::back()->with(
            'status', [
                'type' => 'success',
                'message' => __('Invitation resent successfully!')
            ]
        );
    }

    /**
     * Delete a pending invitation
     */
    public function deleteInvite($id)
    {
        $invite = TeamInvite::where('id', $id)
            ->where('organization_id', session()->get('current_organization'))
            ->first();

        if (!$invite) {
            return Redirect::back()->with(
                'status', [
                    'type' => 'error',
                    'message' => __('Invitation not found!')
                ]
            );
        }

        $invite->delete();

        return Redirect::back()->with(
            'status', [
                'type' => 'success',
                'message' => __('Invitation deleted successfully!')
            ]
        );
    }
}