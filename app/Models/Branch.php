<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{

    protected $fillable = [
        'branch_name',
        'city_id',
        'address',
        'phone',
        'email',
        'latitude',
        'longitude'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function country()
    {
        return $this->through('city')->has('country'); // الدولة التي يوجد بداخلها الفرع
    }
    public function parcel()
    {
        return $this->hasMany(Parcel::class);
    }
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

}
