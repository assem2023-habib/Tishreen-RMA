<?php

namespace App\Filament\Tables\Actions;

use App\Enums\SenderType;
use App\Models\GuestUser;
use Filament\Tables\Actions\Action;

class ViewGuestSenderAction
{
    public static function make(): Action
    {
        return Action::make('viewGuestSender')
            ->label('View Guest User')
            ->icon('heroicon-o-user')
            ->url(fn($record) => route('filament.admin.resources.guest-users.edit', $record->sender_id))
            ->visible(fn($record) => $record->sender_type === SenderType::GUEST_USER);
    }
}
