<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParcelHistoryResource\Pages;
use App\Models\ParcelHistory;
use Filament\Forms\Components\{Textarea, TextInput};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                TextInput::make('user_id')
                    ->label('Modified By')
                    ->disabled()
                    ->formatStateUsing(
                        fn($state, $record)
                        => $record?->user?->name ?? '-'
                    ),

                TextInput::make('parcel_id')
                    ->label('Parcel Sender')
                    ->disabled()
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $record->parcel->sender->name,
                    ),
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
                TextColumn::make('user.name')
                    ->label('Modified By'),

                TextColumn::make('parcel.sender.name')
                    ->label('Parcel Sender'),
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
                Tables\Actions\ViewAction::make('show')
                    ->label('Show'),
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
