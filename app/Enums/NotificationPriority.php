<?php

namespace App\Enums;

enum NotificationPriority: string
{
    // $table->enum('notification_priority', ['Important', 'Reminder', 'Loyalty']); // نوع الأولوية

    case IMPORTANT = 'Important';
    case REMINDER = 'Reminder';
    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
