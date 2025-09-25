<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'branch_route_day_id',
        'truck_id',
        'actual_departure_time',
        'actual_arrival_time',
    ];
    public function branchRoute()
    {
        return $this->belongsTo(BranchRouteDays::class);
    }
    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }
    public function parcelAssignments()
    {
        return $this->hasMany(ParcelShipmentAssignment::class);
    }
}
