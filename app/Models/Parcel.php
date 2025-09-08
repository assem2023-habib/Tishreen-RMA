<?php

namespace App\Models;

use App\Enums\SenderType;
use App\Models\BranchRoute;
use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{

    protected $fillable = [
        'sender_id',
        'sender_type',
        'route_id',
        'reciver_name',
        'reciver_address',
        'reciver_phone',
        'weight',
        'cost',
        'is_paid',
        'parcel_status',
        'tracking_number',
        'price_policy_id',
        'appointment_id',

    ];
    protected $casts = [
        'sender_type' => SenderType::class,
        'weight' => 'float',
        'cost' => 'float',
    ];

    public function route()
    {
        return $this->belongsTo(BranchRoute::class, 'route_id');
    }
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
    public function getRouteLabelAttribute()
    {
        if ($this->route && $this->route->fromBranch && $this->route->toBranch)
            return $this->route->fromBranch->branch_name . '-->' . $this->route->toBranch->branch_name;
        return '-';
    }
    public function sender()
    {
        return $this->morphTo();
    }
    public function parcelsHistories()
    {
        return $this->hasMany(ParcelHistory::class);
    }
    public function pricePolicy()
    {
        return $this->belongsTo(PricingPolicy::class);
    }
    public function parcelAuthorization()
    {
        return $this->hasMany(ParcelAuthorization::class);
    }
    public function rates()
    {
        return $this->morphMany(Rate::class, 'rateable');
    }


    // public function targetBranch()
    // {
    //     return $this->belongsTo(BranchRoute::class, 'from_branch_id');
    // }
    // public function sourceBranch()
    // {
    //     return $this->belongsTo(BranchRoute::class, 'to_branch_id');
    // }

    protected static function booted()
    {
        static::creating(function ($parcel) {
            if (!empty($parcel->weight) && !empty($parcel->price_policy_id)) {
                $pricePolicy = PricingPolicy::find($parcel->price_policy_id);
                $parcel->cost = (float) $parcel->weight * (float) ($pricePolicy->price ?? 0);
            } else {
                $parcel->cost = 2;
            }
        });
    }
}
