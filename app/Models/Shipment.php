<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'branch_route_day_id',
        'actual_departure_time',
        'actual_arrival_time',
    ];
    public function branchRouteDay()
    {
        return $this->belongsTo(BranchRouteDays::class);
    }
    public function trucks()
    {
        return $this->belongsToMany(Truck::class, 'shipment_truck');
    }
    public function parcelAssignments()
    {
        return $this->hasMany(ParcelShipmentAssignment::class);
    }
}
