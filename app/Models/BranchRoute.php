<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class BranchRoute extends Model
{
    protected $fillable = [
        'from_branch_id',
        'to_branch_id',
        //'day',
        'is_active',
        'estimated_departur_time',
        'estimated_arrival_time',
        'distance_per_kilo',
    ];

    // Automatically cast datetime fields to Carbon instances
    protected $casts = [
        'estimated_departur_time' => 'datetime',
        'estimated_arrival_time' => 'datetime',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'route_id');
    }

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    // public function trucks()
    // {
    //     return $this->belongsToMany(Truck::class, 'trucks_branch_routes', 'route_id', 'truck_id');
    // }

    public function branchRouteDays()
    {
        return $this->hasMany(BranchRouteDays::class);
    }

    public function parcels()
    {
        return $this->hasMany(Parcel::class, 'route_id'); // حددنا العمود route_id
    }
    public function getRouteLabelAttribute(): string
    {
        if ($this->fromBranch && $this->toBranch) {
            return $this->fromBranch->branch_name . " --> " . $this->toBranch->branch_name;
        }
        return "-";
    }
}
