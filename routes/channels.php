<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// قناة الإشعارات العامة
Broadcast::channel('notifications', function ($user) {
    return true; // أي مستخدم مسجل يمكنه الوصول
});

// قناة المستخدم الخاصة
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// قناة الإشعارات الخاصة بالمستخدم
Broadcast::channel('user.{id}.notifications', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
