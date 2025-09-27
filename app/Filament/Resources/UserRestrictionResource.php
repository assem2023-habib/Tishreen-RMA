<?php

namespace App\Filament\Resources;

use App\Enums\UserAccountStatus;
use App\Filament\Forms\Components\ActiveToggle;
use App\Filament\Resources\UserRestrictionResource\Pages;
use App\Filament\Tables\Columns\{ActiveToggleColumn, Timestamps};
use App\Models\{User, UserRestriction};
use Filament\Forms\Components\{DatePicker, Grid, Select, Textarea};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserRestrictionResource extends Resource
{
    protected static ?string $model = UserRestriction::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?string $navigationGroup = 'Users';
    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema(
                    [
                        Select::make('user_id')
                            ->label('User')
                            ->options(function () {
                                return User::select('id', 'user_name')
                                    ->get()
                                    ->mapWithKeys(function ($user) {
                                        return [$user->id => $user->user_name];
                                    });
                            }),
                        Select::make('restriction_type')
                            ->label('Restriction Type')
                            ->options(
                                UserAccountStatus::class
                            )
                            ->default(UserAccountStatus::BANED->value),
                    ]
                ),
                Grid::make(1)->schema(
                    [
                        Textarea::make('reason')
                            ->columnSpanFull(),
                    ]
                ),
                Grid::make(2)->schema(
                    [
                        DatePicker::make('starts_at')
                            ->label('Start At')
                            ->native(false)
                            ->default(now())
                            ->required(),
                        DatePicker::make('ends_at')
                            ->label('End At')
                            ->after('starts_at'),
                    ]
                ),
                Grid::make(1)->schema(
                    [
                        ActiveToggle::make('is_active', '...?Restrtiction Activate')
                            ->default(1)
                            ->columnSpanFull(),
                    ]
                )
                    ->extraAttributes(['dir' => 'rtl']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.user_name')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('restriction_type')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        UserAccountStatus::BANED->value => 'danger',
                        UserAccountStatus::FROZEN->value => 'gray',
                    },)
                    ->formatStateUsing(fn(string $state) => __($state))
                    ->icon(fn($state) => match ($state) {
                        UserAccountStatus::BANED->value => 'heroicon-o-x-mark',
                        UserAccountStatus::FROZEN->value => 'heroicon-o-pause',
                    })
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->dateTime('Y-M-d')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime('Y-M-d')
                    ->sortable(),
                ActiveToggleColumn::make('is_active'),

                ...Timestamps::make(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUserRestrictions::route('/'),
            'create' => Pages\CreateUserRestriction::route('/create'),
            'edit' => Pages\EditUserRestriction::route('/{record}/edit'),
        ];
    }
}
