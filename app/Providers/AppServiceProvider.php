<?php

namespace App\Providers;

use App\Models\{GuestUser, User, ParcelHistory};
use App\Observers\{ParcelObserver, UserObserve};
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
        Passport::loadKeysFrom(base_path('app/secrets/oauth'));

        Passport::tokensExpireIn(CarbonInterval::year(1));
        Passport::refreshTokensExpireIn(CarbonInterval::year(1));
        Passport::personalAccessTokensExpireIn(CarbonInterval::year(1));
        Passport::enablePasswordGrant();

        User::observe(UserObserve::class);
        ParcelHistory::observe(ParcelObserver::class);

        // Relation::morphMap([
        //     'user' => User::class,
        //     'guest_user' => GuestUser::class
        // ]);
    }
}
