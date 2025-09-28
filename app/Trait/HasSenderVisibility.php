<?php

namespace App\Trait;

use App\Enums\SenderType;
use Closure;

trait HasSenderVisibility
{

    public static function visibleForGuest($field = 'authorized_user_type', $senderType = SenderType::GUEST_USER->value): Closure
    {
        return fn(callable $get) => $get($field) === $senderType;
    }

    public static function visibleForUser($field = 'authorized_user_type', $senderType = SenderType::AUTHENTICATED_USER->value): Closure
    {
        return fn(callable $get) => $get($field) === $senderType;
    }
}
