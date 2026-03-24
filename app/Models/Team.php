<?php

namespace App\Models;
use App\Http\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model {
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $guarded = [];
    public $timestamps = true;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function tickets()
    {
        return $this->hasMany(ChatTicket::class, 'assigned_to', 'user_id');
    }

    /**
     * Check if team member has a specific permission
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        // Owner has all permissions
        if ($this->role === 'owner') {
            return true;
        }

        // If permissions array is null, use default permissions from config
        if ($this->permissions === null) {
            $defaults = config('team_permissions.defaults.' . $this->role, []);
            return in_array($permission, $defaults);
        }

        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Check if team member has access to a specific route
     *
     * @param string $routePrefix
     * @return bool
     */
    public function hasRouteAccess(string $routePrefix): bool
    {
        // Owner has all permissions
        if ($this->role === 'owner') {
            return true;
        }

        $permissions = config('team_permissions.permissions', []);
        
        foreach ($permissions as $perm) {
            if (in_array($routePrefix, $perm['routes'])) {
                return $this->hasPermission($perm['key']);
            }
        }

        // If route is not in permissions config, allow access
        return true;
    }

    /**
     * Get all permissions for this team member
     *
     * @return array
     */
    public function getAllPermissions(): array
    {
        // Owner has all permissions
        if ($this->role === 'owner') {
            return array_column(config('team_permissions.permissions', []), 'key');
        }

        // Return stored permissions or defaults
        if ($this->permissions !== null) {
            return $this->permissions;
        }

        return config('team_permissions.defaults.' . $this->role, []);
    }

    /**
     * Set permissions for team member
     *
     * @param array $permissions
     * @return void
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
        $this->save();
    }

    /**
     * Get default permissions based on role
     *
     * @return array
     */
    public function getDefaultPermissions(): array
    {
        if ($this->role === 'owner') {
            return array_column(config('team_permissions.permissions', []), 'key');
        }

        return config('team_permissions.defaults.' . $this->role, []);
    }
}
