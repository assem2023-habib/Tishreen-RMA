<?php

namespace App\Models;

use App\Trait\BlamableTrait;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use BlamableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'country_id',
        'ar_name',
        'en_name',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
