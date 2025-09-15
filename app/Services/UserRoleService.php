<?php

namespace App\Services;

use App\Models\Role;
use App\Models\TelegramOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class UserRoleService
{
    public static function hasRole(User $user, string $roleName)
    {
        return $user->roles()->where('role_name', $roleName)->exists();
    }
    public static function assignRole(User $user, Role $role)
    {
        if (!self::hasRole($user, $role->role_name)) {
            $user->roles()->syncWithoutDetaching([$role->id]);
            return true;
        }
        return false;
    }
    public static function removeRole(User $user, Role $role)
    {
        if (self::hasRole($user, $role->role_name)) {
            $user->roles()->detach($role->id);
            return true;
        }
        return false;
    }
}
