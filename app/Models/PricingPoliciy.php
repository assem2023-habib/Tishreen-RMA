<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPoliciy extends Model
{

    protected $fillable = [
        'price',
        'price_unit',
        'weight_limit_min',
        'weight_limit_max',
        'currency',
    ];
    public function parcels()
    {
        return $this->hasMany(Parcel::class);
    }
}
