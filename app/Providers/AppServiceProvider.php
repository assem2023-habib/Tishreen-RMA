<?php

namespace App\Providers;

use App\Enums\RatingForType;
use App\Enums\SenderType;
use App\Models\{Appointment, Branch, Employee, Parcel, ParcelAuthorization, User, GuestUser};
use App\Observers\{AppointmentObserver, EmployeeObserver, UserObserve, ParcelAuthorizationObserver, ParcelLifecycleObserver, ParcelObserver};
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Database\Eloquent\Relations\BelongsToMany::macro('unread', function () {
            return $this->whereNull('notification_user.read_at');
        });

        \Illuminate\Database\Eloquent\Relations\BelongsToMany::macro('markAsRead', function () {
            return $this->updateExistingPivot($this->allRelatedIds(), [
                'read_at' => now(),
            ]);
        });

        Relation::morphMap([
            SenderType::GUEST_USER->value => GuestUser::class,
            SenderType::AUTHENTICATED_USER->value => User::class,
        ]);
        Relation::morphMap([
            RatingForType::BRANCH->value => Branch::class,
            RatingForType::EMPLOYEE->value => Employee::class,
            RatingForType::PARCEL->value => Parcel::class,
        ]);

        Passport::loadKeysFrom(base_path('app/secrets/oauth'));

        Passport::tokensExpireIn(CarbonInterval::year(1));
        Passport::refreshTokensExpireIn(CarbonInterval::year(1));
        Passport::personalAccessTokensExpireIn(CarbonInterval::year(1));
        Passport::enablePasswordGrant();

        User::observe(UserObserve::class);
        Parcel::observe(ParcelLifecycleObserver::class);
        ParcelAuthorization::observe(ParcelAuthorizationObserver::class);
        Employee::observe(EmployeeObserver::class);
        Parcel::observe(ParcelObserver::class);
        Appointment::observe(AppointmentObserver::class);
    }
}
