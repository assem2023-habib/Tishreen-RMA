<?php

namespace App\Filament\Resources;

use App\Enums\RatingForType;
use App\Filament\Resources\RateResource\{Pages, RelationManagers};
use App\Models\{Employee, Rate, User, Branch};
use Filament\Forms;
use Filament\Forms\Components\{Textarea, Select, TextInput};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class RateResource extends Resource
{
    protected static ?string $model = Rate::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-thumb-up';

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
        return $table
            // ->query(
            //     static::getEloquentQuery()->with('rateable'),

            // )
            ->columns([
                TextColumn::make('user.user_name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rateable_name')
                    ->label('Rateable Name')
                    ->getStateUsing(function ($record) {
                        if ($record->rateable_type === RatingForType::APPLICATION->value) {
                            return 'Application : ' . $record->rateable_id;
                        }
                        if ($record->rateable_type === RatingForType::BRANCH->value) {
                            $banch = Branch::select('id', 'branch_name')->where('id', '=', $record->rateable_id)->get();
                            return 'Branch : ' . $banch;
                        }
                        if ($record->rateable_type === RatingForType::EMPLOYEE->value) {
                            $employee = Employee::select('id', 'user_id')
                                ->where('id', $record->rateable_id)
                                ->with('user')
                                ->first();
                            return 'Employee : ' . $employee->user->first_name . ' ' . $employee->user->last_name;
                        }
                        if ($record->rateable_type === RatingForType::SERVICE->value) {
                            return 'Service : ' . $record->rateable_id;
                        }
                        if ($record->rateable_type === RatingForType::DELIVERY->value) {
                            return 'Delivery : ' . $record->rateable_id;
                        }

                        if ($record->rateable_type === RatingForType::CHATSESSION->value) {
                            return 'ChatSession : ' . $record->rateable_id;
                        }

                        if (! $record->rateable) {
                            return '-';
                        }
                    })
                    ->sortable()
                    ->searchable(),
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
                    ->color(function (string $state) {
                        match ($state) {
                            RatingForType::APPLICATION => 'success',
                            RatingForType::SERVICE => 'gray',
                            RatingForType::BRANCH => 'danger',
                            default => 'secondary',
                        };
                    })
                    ->formatStateUsing(fn(string $state) => __($state)),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('rating')
                    ->label('Rating'),

                TextEntry::make('comment')
                    ->label('Comment'),

                TextEntry::make('user.user_name')
                    ->label('Rated By'),

                TextEntry::make('rateable_type')
                    ->label('Type'),

                TextEntry::make('relatedDetails')
                    ->label('Related Details'),

                TextEntry::make('created_at')
                    ->label('Created At'),
            ]);
    }
}
