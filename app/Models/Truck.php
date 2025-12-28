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
    public function shipments()
    {
        return $this->belongsToMany(Shipment::class, 'shipment_truck');
    }
    public function branchRouteDays()
    {
        return $this->belongsToMany(
            BranchRouteDays::class,
            'route_day_truck_assignments',
            'truck_id',
            'branch_route_day_id'
        );
    }
}
