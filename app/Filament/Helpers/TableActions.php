<?php

namespace App\Filament\Helpers;

use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;

class TableActions
{
    public static function default($viewName = 'View', $editName = 'Edit', $DeleteName = 'Delete'): ActionGroup
    {
        return ActionGroup::make([
            Tables\Actions\ViewAction::make()
                ->label($viewName)
                ->color('secondary'),
            Tables\Actions\EditAction::make()
                ->label($editName)
                ->color('warning'),
            Tables\Actions\DeleteAction::make()
                ->label($DeleteName)
                ->color('danger'),
        ]);
    }
}
