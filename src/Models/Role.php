<?php namespace WebEd\Base\ACL\Models;

use WebEd\Base\ACL\Models\Contracts\RoleModelContract;
use WebEd\Base\Models\EloquentBase as BaseModel;
use WebEd\Base\Users\Models\User;

class Role extends BaseModel implements RoleModelContract
{
    protected $table = 'we_roles';

    protected $primaryKey = 'id';

    protected $fillable = ['name', 'slug', 'created_by', 'updated_by'];

    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'we_roles_permissions', 'role_id', 'permission_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'we_users_roles', 'role_id', 'user_id');
    }

    /**
     * Setter
     * @param $value
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = str_slug($value);
    }
}
