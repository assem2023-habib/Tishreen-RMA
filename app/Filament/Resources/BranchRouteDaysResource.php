<?php

namespace App\Filament\Resources;

use App\Enums\DaysOfWeek;
use App\Filament\Resources\BranchRouteDaysResource\Pages;
use App\Filament\Resources\BranchRouteDaysResource\RelationManagers;
use App\Models\BranchRoute;
use App\Models\BranchRouteDays;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BranchRouteDaysResource extends Resource
{
    protected static ?string $model = BranchRouteDays::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = "Transport";
    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('day_of_week')
                    ->options(DaysOfWeek::values())
                    ->required(),
                Select::make('branch_route_id')
                    ->options(function () {
                        return BranchRoute::select('id', 'from_branch_id', 'to_branch_id')
                            ->get()
                            ->mapWithKeys(function ($branchRoute) {
                                return [$branchRoute->id => $branchRoute->fromBranch->branch_name . ', ' . $branchRoute->toBranch->branch_name];
                            });
                    })
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day_of_week'),
                TextColumn::make('branch_route_id.branchRote.from_branch_id')
                    ->sortable(),
                TextColumn::make('branch_route_id.branchRote.to_branch_id')
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
            'index' => Pages\ListBranchRouteDays::route('/'),
            'create' => Pages\CreateBranchRouteDays::route('/create'),
            'edit' => Pages\EditBranchRouteDays::route('/{record}/edit'),
        ];
    }
}
