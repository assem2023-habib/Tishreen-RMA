<?php

namespace App\Providers;

use App\Enums\SenderType;
use App\Models\Notification;
use App\Models\{Employee, Parcel, ParcelAuthorization, User};
use App\Observers\NotificationObserver;
use App\Observers\ParcelLifecycleObserver;
use App\Observers\{EmployeeObserver, UserObserve, ParcelAuthorizationObserver};
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
        Relation::morphMap([
            SenderType::GUEST_USER->value => \App\Models\GuestUser::class,
            SenderType::AUTHENTICATED_USER->value => \App\Models\User::class,
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
        Notification::observe(NotificationObserver::class);
    }
}
