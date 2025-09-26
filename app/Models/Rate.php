<?php

namespace App\Models;

use App\Enums\RatingForType;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = [
        'user_id',
        'rateable_id',
        'rating',
        'comment',
        'rateable_type',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function rateable()
    {
        return $this->morphTo();
    }
    public function getRateableNameAttribute(): string
    {
        return match ($this->rateable_type) {
            RatingForType::APPLICATION->value => 'Application Rating',
            RatingForType::SERVICE->value     => 'Service Rating',
            RatingForType::DELIVERY->value    => 'Delivery Rating',
            RatingForType::CHATSESSION->value => 'Chat Session Rating',
            RatingForType::BRANCH->value => "Branch Name : " . $this->rateable->branch_name,
            RatingForType::EMPLOYEE->value => "Employe Name : " . $this->rateable->user->user_name,
            RatingForType::PARCEL->value => "Parcel Sender Name : " . $this->rateable->sender->user_name,
            default => $this->rateable
                ? class_basename($this->rateable) . ' #' . $this->rateable_id
                : 'General Rating',
        };
    }
}
