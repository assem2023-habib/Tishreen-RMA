<?php

use App\Models\Conversation;
use App\Models\Employee;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('notifications', function () {
    return true;
});

// قناة المحادثة - يمكن للعميل أو الموظف المعين الوصول
Broadcast::channel('conversation.{id}', function ($user, $id) {
    $conversation = Conversation::find($id);
    
    if (!$conversation) {
        return false;
    }
    
    // العميل صاحب المحادثة
    if ($conversation->customer_id === $user->id) {
        return true;
    }
    
    // الموظف المعين للمحادثة
    $employee = Employee::where('user_id', $user->id)->first();
    if ($employee && $conversation->employee_id === $employee->id) {
        return true;
    }
    
    // المدير العام يمكنه الوصول لجميع المحادثات
    if ($user->hasRole('super_admin')) {
        return true;
    }
    
    return false;
});

// قناة الموظف الخاصة
Broadcast::channel('employee.{id}', function ($user, $id) {
    $employee = Employee::where('user_id', $user->id)->first();
    return $employee && (int) $employee->id === (int) $id;
});

// قناة طابور الدعم - لجميع الموظفين لرؤية المحادثات الجديدة
Broadcast::channel('support.queue', function ($user) {
    // يمكن لأي موظف الاشتراك في هذه القناة
    $employee = Employee::where('user_id', $user->id)->first();
    return $employee !== null || $user->hasRole('super_admin');
});

