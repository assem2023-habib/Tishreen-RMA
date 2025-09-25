<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParcelShipmentAssignment extends Model
{
    protected $fillable = [
        'parcel_id',
        'shipment_id',
        'pick_up_confirmed_by_emp_id',
        'pick_up_confirmed_date',
        'delivery_confirmed_by_emp_id',
        'delivery_confirmed_date',
    ];
    protected $casts = [
        'pick_up_confirmed_date' => 'datetime:H:i',
        'delivery_confirmed_date'  => 'datetime:H:i',
    ];
    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
    /**
     * الموظف الذي استلم
     */
    public function receivedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'pick_up_confirmed_by_emp_id');
    }
    /**
     * الموظف الذي سلّم
     */
    public function deliveredByEmployee()
    {
        return $this->belongsTo(Employee::class, 'delivery_confirmed_by_emp_id');
    }
}
