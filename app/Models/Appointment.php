<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'branch_id',
        'route_id',
        'date',
        'time',
        'available_slots',
    ];

    public function route()
    {
        return $this->belongsTo(BranchRoute::class, 'route_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function parcels()
    {
        return $this->hasMany(Parcel::class, 'appointment_id');
    }
}
