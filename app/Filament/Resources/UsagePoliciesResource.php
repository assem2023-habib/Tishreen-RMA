<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsagePoliciesResource\Pages;
use App\Models\UsagePolicies;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;

class UsagePoliciesResource extends Resource
{
    protected static ?string $model = UsagePolicies::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = "Support & Information";

    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('policy_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('policy_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('policy_description')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('policy_type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('policy_name')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('edite policy'),
                    Tables\Actions\ViewAction::make()
                        ->label('view ploicy'),
                    Tables\Actions\DeleteAction::make()
                        ->label('delete policy'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsagePolicies::route('/'),
            'create' => Pages\CreateUsagePolicies::route('/create'),
            'edit' => Pages\EditUsagePolicies::route('/{record}/edit'),
        ];
    }
}
