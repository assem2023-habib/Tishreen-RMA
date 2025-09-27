<?php

namespace App\Filament\Helpers;

use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;

class TableActions
{
    public static function default(): ActionGroup
    {
        return ActionGroup::make([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }
}
