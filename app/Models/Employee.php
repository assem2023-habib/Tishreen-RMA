<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'branch_id',
        'beging_date',
        'end_date',
        'status'
    ];
    protected $cast = [
        'beging_date' => 'datetime:H:i',
        'end_date' => 'datetime:H:i',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function trucks()
    {
        return $this->hasMany(Truck::class);
    }
    public function parcelsConfirmedPickUpFromSender()
    {

        return $this->hasMany(ParcelShipmentAssignment::class, 'pick_up_confirmed_by_emp_id');
    }
    public function parcelsConfirmedDeliveredToReciver()
    {
        return $this->hasMany(ParcelShipmentAssignment::class, 'delivery_confirmed_by_emp_id');
    }
    public function rates()
    {
        return $this->morphMany(Rate::class, 'rateable');
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}
