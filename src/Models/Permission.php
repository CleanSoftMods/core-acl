<?php namespace WebEd\Base\ACL\Models;

use WebEd\Base\ACL\Models\Contracts\PermissionModelContract;
use WebEd\Base\Models\EloquentBase as BaseModel;

class Permission extends BaseModel implements PermissionModelContract
{
    protected $table = 'we_permissions';

    protected $primaryKey = 'id';

    protected $fillable = ['name', 'slug', 'module'];

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'we_roles_permissions', 'permission_id', 'role_id');
    }
}
