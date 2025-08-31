<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\TelegramOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class UserPermissionService
{
    public static function can(User $user, string $PermissionName)
    {
        return $user->permissions()->where('permission_name', $PermissionName)->exists();
    }
    public static function assignPermission(User $user, Permission $permission)
    {
        if (!self::can($user, $permission->permission_name)) {
            $user->permissions()->syncWithoutDetaching([$permission->id]);
            return true;
        }
        return false;
    }
    public static function removePermission(User $user, Permission $permission)
    {
        if (self::can(user: $user, PermissionName: $permission->permission_name)) {
            $user->permissions()->detach($permission->id);
            return true;
        }
        return false;
    }
}
