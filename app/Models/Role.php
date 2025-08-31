<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'role_name',
        'description',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'roles_users');
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions');
    }
}
