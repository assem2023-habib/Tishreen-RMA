<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestUser extends Model
{

    protected $fillable = [
        'city_id',
        'first_name',
        'last_name',
        'phone',
        'address',
        'national_number',
        'birthday',
    ];

    public function parcels()
    {
        return $this->morphMany(Parcel::class, 'sender');
    }
    public function parcelsAuthorizations()
    {
        return $this->morphMany(ParcelAuthorization::class, 'authorizedUser');
    }
    public function sender()
    {
        return  $this->morphMany(Parcel::class, 'sender');
    }
}
