<?php

namespace App\Filament\Resources;

use App\Enums\RatingForType;
use App\Filament\Resources\RateResource\{Pages, RelationManagers};
use App\Filament\Tables\Columns\Timestamps;
use App\Models\{Employee, Rate, User, Branch};
use Filament\Forms;
use Filament\Forms\Components\{Textarea, Select, TextInput};
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RateResource extends Resource
{
    protected static ?string $model = Rate::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-thumb-up';
    protected static ?string $navigationGroup = 'Rates and comments';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('User')
                    ->options(function () {
                        return User::select('id', 'user_name')
                            ->get()
                            ->mapWithKeys(function ($user) {
                                return [$user->id => $user->user_name];
                            });
                    }),
                Forms\Components\TextInput::make('rateable_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('rating')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('comment')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('rateable_type')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('user.user_name')
                ->label('User')
                ->searchable()
                ->sortable(),
            TextColumn::make('rateable_name')
                ->label('Rateable Name'),
            TextColumn::make('rating')
                ->label('Rating')
                ->formatStateUsing(callback: function ($state) {
                    $stars = str_repeat('⭐', (int) $state);
                    return $stars;
                })->html()
                ->sortable(),
            TextColumn::make('rateable_type')
                ->label('Rateable Type')
                ->badge()
                ->color(fn($state) => RatingForType::color($state))
                ->formatStateUsing(fn($state) => RatingForType::label($state)),
            ...Timestamps::make(),
        ])
            ->filters([])
            ->actions([])
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
            'index' => Pages\ListRates::route('/'),
            'create' => Pages\CreateRate::route('/create'),
            // 'edit' => Pages\EditRate::route('/{record}/edit'),
            'view' => Pages\ViewRate::route('/{record}'),
        ];
    }
}
