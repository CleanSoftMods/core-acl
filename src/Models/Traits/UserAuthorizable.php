<?php namespace WebEd\Base\ACL\Models\Traits;

trait UserAuthorizable
{
    /**
     * Set relationship
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(\WebEd\Base\ACL\Models\Role::class, 'we_users_roles', 'user_id', 'role_id');
    }

    /**
     * Get all roles and permissions of current user
     */
    public function setupUser()
    {
        if (!$this->id || check_user_acl()->getRoles($this->id)) {
            return;
        }

        $relatedRoles = $this->roles()->select('slug')->get()->pluck('slug')->toArray();
        check_user_acl()->pushRoles($this->id, $relatedRoles);

        $relatedPermissions = static::join('we_users_roles', 'we_users_roles.user_id', '=', 'we_users.id')
            ->join('we_roles', 'we_users_roles.role_id', '=', 'we_roles.id')
            ->join('we_roles_permissions', 'we_roles_permissions.role_id', '=', 'we_roles.id')
            ->join('we_permissions', 'we_roles_permissions.permission_id', '=', 'we_permissions.id')
            ->where('we_users.id', '=', $this->id)
            ->distinct()
            ->groupBy('we_permissions.id', 'we_permissions.slug')
            ->select('we_permissions.slug', 'we_permissions.id')
            ->get()
            ->pluck('slug')
            ->toArray();
        check_user_acl()->pushPermissions($this->id, $relatedPermissions);
    }

    /**
     * @return bool
     */
    public function isSuperAdmin()
    {
        $this->setupUser();

        if (check_user_acl()->hasRoles($this->id, ['super-admin'])) {
            return true;
        }
        return false;
    }

    /**
     * @param array|string $roles
     * @return bool
     */
    public function hasRole($roles)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!is_array($roles)) {
            $roles = func_get_args();
        }

        if (!$roles) {
            return true;
        }

        $roles = array_values($roles);

        if (check_user_acl()->hasRoles($this->id, $roles)) {
            return true;
        }

        return false;
    }

    /**
     * @param string|array $permissions
     * @return bool
     */
    public function hasPermission($permissions)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!is_array($permissions)) {
            $permissions = func_get_args();
        }

        if (!$permissions) {
            return true;
        }

        $permissions = array_values($permissions);

        if (check_user_acl()->hasPermissions($this->id, $permissions)) {
            return true;
        }

        return false;
    }
}
