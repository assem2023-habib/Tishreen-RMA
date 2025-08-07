<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchRouteDays extends Model
{
    protected $fillable = [
        'day_of_week',
        'branch_route_id',
    ];
    public function branchRoute()
    {
        return $this->belongsTo(BranchRoute::class);
    }
}
