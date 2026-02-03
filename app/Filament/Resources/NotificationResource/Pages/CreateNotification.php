<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Filament\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use App\Models\User;
use App\Notifications\SendNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class CreateNotification extends CreateRecord
{
    protected static string $resource = NotificationResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;
        $userIds = $this->data['user_ids'] ?? [];

        if (!empty($userIds)) {
            $users = User::whereIn('id', $userIds)->get();
            
            NotificationFacade::send($users, new SendNotification([
                'title' => $record->title,
                'message' => $record->message,
                'notification_type' => $record->notification_type,
                'notification_priority' => $record->notification_priority,
                'user_ids' => $userIds,
            ]));
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
