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
    ];
    protected $casts = [
        'sender_type' => SenderType::class,
    ];
    // public function targetBranch()
    // {
    //     return $this->belongsTo(BranchRoute::class, 'from_branch_id');
    // }
    // public function sourceBranch()
    // {
    //     return $this->belongsTo(BranchRoute::class, 'to_branch_id');
    // }
    public function route()
    {
        return $this->belongsTo(BranchRoute::class);
    }
    public function routeLabel()
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
    public function rates()
    {
        return $this->morphMany(Rate::class, 'rateable');
    }
}
