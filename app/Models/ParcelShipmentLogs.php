<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParcelShipmentLogs extends Model
{
    protected $fillable = [
        'parcel_id',
        'pick_up_confirmed_by_emp_id',
        'delivery_confirmed_by_emp_id',
        'truck_id',
        'pick_up_confiremd_date',
        'delivery_confirmed_date',
        'assigned_truck_date',
    ];
    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }
    public function empConfirmedPickupFromSender()
    {
        return $this->belongsTo(Employee::class);
    }
    public function empConfirmedDelveriedToReciver()
    {
        return $this->belongsTo(Employee::class);
    }
    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }
}
