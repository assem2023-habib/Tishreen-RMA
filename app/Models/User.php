<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Models\Branch;
use App\Models\Employee;
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
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasName, OAuthenticatable, MustVerifyEmail, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

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
        'image_profile',
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
        if ($this->hasRole(RoleName::SUPER_ADMIN->value) || $this->hasPermissionTo(PermissionName::CAN_ACCESS_PANEL->value)) {
            return true;
        }
        return false;
    }
    public function rates()
    {
        return $this->morphMany(Rate::class, 'rateable');
    }
    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_user')
            ->withPivot(['is_read', 'read_at'])
            ->withTimestamps();
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }


    // user can book many appointment
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }
}
