<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParcelHistoryResource\Pages;
use App\Filament\Resources\ParcelHistoryResource\RelationManagers;
use App\Models\ParcelHistory;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParcelHistoryResource extends Resource
{
    protected static ?string $model = ParcelHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Parcels';
    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('modified_by')
                    ->label('Modified By')
                    ->disabled()
                    ->formatStateUsing(fn($state, $record) => $record?->user?->user_name ?? '-'),

                TextInput::make('parcel_sender')
                    ->label('Parcel Sender')
                    ->disabled()
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $record?->parcel?->sender instanceof \App\Models\User
                            ? $record->parcel->sender->user_name
                            : '-'
                    ),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Textarea::make('old_data')
                    ->columnSpanFull()
                    ->formatStateUsing(fn($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                    ->disabled(),

                Textarea::make('new_data')
                    ->columnSpanFull()
                    ->formatStateUsing(fn($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                    ->disabled(),

                Textarea::make('changes')
                    ->columnSpanFull()
                    ->formatStateUsing(fn($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                    ->disabled(),
                TextInput::make('operation_type')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('modified_by')
                    ->label('Modified By')
                    ->getStateUsing(fn($record) => $record->user?->user_name ?? '-'),

                TextColumn::make('parcel_sender')
                    ->label('Parcel Sender')
                    ->getStateUsing(
                        fn($record) =>
                        $record->parcel?->sender instanceof \App\Models\User
                            ? $record->parcel->sender->user_name
                            : '-'
                    ),
                TextColumn::make('changes')
                    ->label('Changes')
                    ->formatStateUsing(fn($state) => is_array($state) ? json_encode($state, JSON_UNESCAPED_UNICODE) : $state)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('operation_type')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make('show'),
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
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'parcel.sender']);
    }
}
