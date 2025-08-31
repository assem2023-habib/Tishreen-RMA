<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'permission_name',
        'description'
    ];
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_permissions');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_permissions');
    }
}
