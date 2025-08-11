<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParcelHistoryResource\Pages;
use App\Filament\Resources\ParcelHistoryResource\RelationManagers;
use App\Models\ParcelHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParcelHistoryResource extends Resource
{
    protected static ?string $model = ParcelHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Parcels';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('parcel_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('old_data')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('new_data')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('changes')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('operation_type')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('parcel_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('changes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('operation_type'),
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListParcelHistories::route('/'),
            // 'create' => Pages\CreateParcelHistory::route('/create'),
            // 'edit' => Pages\EditParcelHistory::route('/{record}/edit'),
        ];
    }
}
