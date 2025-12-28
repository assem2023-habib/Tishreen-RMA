<?php

namespace App\Models;

use App\Enums\SenderType;
use App\Models\BranchRoute;
use App\Notifications\GeneralNotification;
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
    public function sender()
    {
        return $this->morphTo();
    }
    public function parcelsHistories()
    {
        return $this->hasMany(ParcelHistory::class);
    }
    public function parcelAuthorization()
    {
        return $this->hasMany(ParcelAuthorization::class);
    }
    public function rates()
    {
        return $this->morphMany(Rate::class, 'rateable');
    }
    public function getSenderNameAttribute()
    {
        return $this->sender->user_name ?? ($this->sender->first_name . $this->sender->last_name);
    }
    public function getRouteLabelAttribute()
    {
        if ($this->route && $this->route->fromBranch && $this->route->toBranch)
            return $this->route->fromBranch->branch_name . '-->' . $this->route->toBranch->branch_name;
        return '-';
    }
}
