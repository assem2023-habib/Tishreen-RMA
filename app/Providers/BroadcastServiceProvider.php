<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes(['middleware' => ['auth:api']]);

        // تعريف القنوات الخاصة
        Broadcast::channel('user.{id}', function ($user, $id) {
            return (int) $user->id === (int) $id;
        });

        // قناة الإشعارات العامة
        Broadcast::channel('notifications', function ($user) {
            return true; // أي مستخدم مسجل يمكنه الوصول
        });

        // قناة الإشعارات الخاصة بالمستخدم
        Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
            return (int) $user->id === (int) $id;
        });
    }
}
