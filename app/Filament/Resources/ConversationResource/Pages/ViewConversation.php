<?php

namespace App\Filament\Resources\ConversationResource\Pages;

use App\Filament\Resources\ConversationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewConversation extends ViewRecord
{
    protected static string $resource = ConversationResource::class;

    protected static string $view = 'filament.resources.conversation-resource.pages.view-conversation';

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('close')
                ->label('إغلاق المحادثة')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->status !== 'closed')
                ->action(function ($record) {
                    $record->close();
                    
                    // Broadcast update to customer
                    broadcast(new \App\Events\ConversationUpdatedEvent($record, 'closed'));
                    
                    $this->refresh();
                }),
        ];
    }
}
