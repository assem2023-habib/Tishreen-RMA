<?php

namespace App\Filament\Resources;

use App\Enums\UserAccountStatus;
use App\Filament\Resources\UserRestrictionResource\Pages;
use App\Filament\Resources\UserRestrictionResource\RelationManagers;
use App\Models\User;
use App\Models\UserRestriction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                            ->default(UserAccountStatus::BANED),
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
                        Toggle::make('is_active')
                            ->label('...?Restrtiction Activate')
                            ->onIcon('heroicon-o-check-circle')
                            ->offIcon('heroicon-o-no-symbol')
                            ->onColor('success')
                            ->offColor('danger')
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
                ToggleColumn::make('is_active')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-no-symbol')
                    ->onColor('success')
                    ->offColor('danger')
                    ->sortable(),
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
            'index' => Pages\ListUserRestrictions::route('/'),
            'create' => Pages\CreateUserRestriction::route('/create'),
            'edit' => Pages\EditUserRestriction::route('/{record}/edit'),
        ];
    }
}
