<?php

namespace App\Filament\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

class Timestamps
{
    public static function make(): array
    {
        return [
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
