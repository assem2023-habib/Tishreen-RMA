<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParcelHistory extends Model
{
    protected $fillable = [
        'parcels_id',
        'old_data',
        'new_data',
        'changes',
        'operation_type',
    ];
    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }
}
