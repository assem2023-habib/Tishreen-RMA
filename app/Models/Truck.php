<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    protected $fillable = [
        'driver_id',
        'truck_number',
        'truck_model',
        'capacity_per_kilo_gram',
        'is_active',
    ];
    public function driver()
    {
        return $this->belongsTo(Employee::class);
    }
    // public function routes()
    // {
    //     return $this->belongsToMany(BranchRoute::class);
    // }
    public function branchRouteDays()
    {
        return $this->belongsToMany(BranchRouteDays::class, 'trucks_branch_routes_days', 'truck_id', 'branch_route_day_id');
    }
}
