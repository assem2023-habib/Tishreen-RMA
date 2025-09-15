<?php

namespace App\Filament\Resources\FrequentlyAskedQuestionsResource\Pages;

use App\Filament\Resources\FrequentlyAskedQuestionsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFrequentlyAskedQuestions extends EditRecord
{
    protected static string $resource = FrequentlyAskedQuestionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
