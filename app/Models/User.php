<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Parcel;
use App\Trait\BlamableTrait;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements HasName, OAuthenticatable, MustVerifyEmail, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'user_name',
        'password',
        'city_id',
        'address',
        'phone',
        'national_number',
        'birthday',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',

    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function getFilamentName(): string
    {
        return $this->user_name ?? $this->email;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ApiResetPasswordNotification($token));
    }
    public function parcels()
    {
        return $this->morphMany(Parcel::class, 'sender');
    }
    public function parcelsAuthorizations()
    {
        return $this->morphMany(ParcelAuthorization::class, 'authorizedUser');
    }
    public function parcelAuthorization()
    {
        return $this->hasMany(ParcelAuthorization::class);
    }
    public function restrictionType()
    {
        return $this->hasMany(UserRestriction::class);
    }
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
    public function canAccessPanel(Panel $panel): bool
    {
        // return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail()
        return true;
    }
}
