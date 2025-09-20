<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchRouteDays extends Model
{
    protected $fillable = [
        'day_of_week',
        'branch_route_id',
        'estimated_departur_time',
        'estimated_arrival_time'
    ];

    protected $casts = [
        'estimated_departur_time' => 'datetime:H:i',
        'estimated_arrival_time'  => 'datetime:H:i',
    ];
    public function branchRoute()
    {
        return $this->belongsTo(BranchRoute::class);
    }
    public function trucks()
    {
        return $this->belongsToMany(Truck::class, 'branch_route_day_truck')
            ->withTimestamps();
    }
}
