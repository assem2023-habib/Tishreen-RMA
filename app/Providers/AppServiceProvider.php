<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Carbon\CarbonInterval;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

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
    }
}
