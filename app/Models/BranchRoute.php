<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchRoute extends Model
{
    protected $fillable = [
        'from_branch_id',
        'to_branch_id',
        'day',
        'is_active',
        'estimated_departur_time',
        'estimated_arrival_time',
        'distance_per_kilo',
    ];
    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }
    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }
    public function trucks()
    {
        return $this->belongsToMany(Truck::class, 'trucks_branch_routes', 'route_id', 'truck_id');
    }
    public function branchRouteDays()
    {
        return $this->hasMany(BranchRouteDays::class);
    }
}
