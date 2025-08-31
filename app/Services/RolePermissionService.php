<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\TelegramOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class RolePermissionService
{
    public static  function can(Role $role, string $PermissionName)
    {
        return $role->permissions()->where('permission_name', $PermissionName)->exists();
    }
    public static  function assignPermission(Role $role, Permission $permission)
    {
        if (!self::can($role, $permission->permission_name)) {
            $role->permissions()->syncWithoutDetaching([$permission->id]);
            return true;
        }
        return false;
    }
    public static function removePermission(Role $role, Permission $permission)
    {
        if (self::can($role, PermissionName: $permission->permission_name)) {
            $role->permissions()->detach($permission->id);
            return true;
        }
        return false;
    }
}
