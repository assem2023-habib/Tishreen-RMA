<?php

namespace App\Filament\Resources\CountryResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;

use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms;
use Filament\Tables\Columns\TextColumn;

class CityRelationManager extends RelationManager
{
    protected static string $relationship = 'cities'; // اسم العلاقة في موديل Country

    protected static ?string $title = 'cities';
    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ar_name')->label('City arbic name')->required(),
                Forms\Components\TextInput::make('en_name')->label('City english name')->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('ar_name')->label('City arbic name'),
                TextColumn::make('en_name')->label('City english name'),
            ])
            ->headerActions([
                CreateAction::make()->label('Add City'),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ]);
    }
}
