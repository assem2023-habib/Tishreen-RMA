<?php

namespace App\Models;

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
        'price_policy',
        'is_paid',
        'parcel_status',
        'tracking_number',
    ];
    public function targetBranch()
    {
        return $this->belongsTo(BranchRoute::class, 'from_branch_id');
    }
    public function sourceBranch()
    {
        return $this->belongsTo(BranchRoute::class, 'to_branch_id');
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
        return $this->belongsTo(PricingPoliciy::class);
    }
}
