<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParcelHistory extends Model
{
    protected $fillable = [
        'old_data',
        'new_data',
        'changes',
        'operation_type',
        'parcel_id',
        'user_id',
    ];
    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'changes' => 'array',
    ];
    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
