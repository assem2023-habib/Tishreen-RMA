<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsagePolicies extends Model
{
    protected $fillable = [
        'policy_type',
        'policy_name',
        'policy_description',
    ];
}
