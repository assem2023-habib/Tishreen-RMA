<?php

namespace App\Filament\Resources;

use App\Enums\DaysOfWeek;
use App\Filament\Resources\BranchRouteDaysResource\Pages;
use App\Filament\Resources\BranchRouteDaysResource\RelationManagers;
use App\Models\BranchRoute;
use App\Models\BranchRouteDays;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BranchRouteDaysResource extends Resource
{
    protected static ?string $model = BranchRouteDays::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Transport';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('day_of_week')
                    ->label('Day')
                    ->options(DaysOfWeek::class)
                    ->multiple()
                    ->required(),
                Select::make('branch_route_id')
                    ->label('Brand Route')
                    ->options(
                        function () {
                            return BranchRoute::select('id', 'from_branch_id', 'to_branch_id', 'is_active')
                                ->where('is_active', 1)
                                ->get()
                                ->mapWithKeys(
                                    function ($branchRoute) {
                                        return [$branchRoute->id => $branchRoute->fromBranch->branch_name . " --> " . $branchRoute->toBranch->branch_name];
                                    }
                                );
                        }
                    )
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day_of_week')
                    ->badge()
                    ->colors([
                        'danger' => DaysOfWeek::SUNDAY->value,
                        'primary' => DaysOfWeek::MONDAY->value,
                        'success' => DaysOfWeek::TUESDAY->value,
                        'warning' => DaysOfWeek::WEDNESDAY->value,
                        'info' => DaysOfWeek::THURSDAY->value,
                        'secondary' => DaysOfWeek::FRIDAY->value,
                        'gray' => DaysOfWeek::SATURDAY->value,
                    ]),
                TextColumn::make('branchRoute.routeLabel')
                    ->label('Route')
                    ->badge()
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListBranchRouteDays::route('/'),
            'create' => Pages\CreateBranchRouteDays::route('/create'),
            'edit' => Pages\EditBranchRouteDays::route('/{record}/edit'),
        ];
    }
}
