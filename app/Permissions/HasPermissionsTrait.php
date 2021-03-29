<?php

namespace App\Permissions;

use App\Models\Role;
use App\Models\Permission;

trait HasPermissionsTrait 
{
    public function hasRole(...$roles)
    {
        foreach ($roles as $role) {
            if ($this->roles->contains('name', $role)) {
                return true;
            }
        }

        return false;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermissionTo($permission)
    {
        // return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
        return $this->hasPermissionThroughRole($permission);
    }

    protected function hasPermissionThroughRole($permission)
    {
        foreach ($permission->roles as $role) {
            if ($this->roles->contains('name', $role->name)) {
                return true;
            }
        }
        
        return false;
    }

    protected function hasPermission($permission)
    {
        return (bool) $this->permissions->where('name', $permission->name)->count();
    }
}