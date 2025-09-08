<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPolicy extends Model
{

    protected $fillable = [
        'policy_type',
        'price',
        'price_unit',
        'limit_min',
        'limit_max',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'limit_min' => 'float',
        'limit_max' => 'float',
    ];
    public function parcels()
    {
        return $this->hasMany(Parcel::class);
    }
}
