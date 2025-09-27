<?php

namespace App\Filament\Resources;

use App\Enums\DaysOfWeek;
use App\Filament\Resources\BranchRouteResource\Pages;
use App\Filament\Tables\Actions\MarkAsArrivedAction;
use App\Filament\Tables\Columns\ActiveToggleColumn;
use App\Models\Branch;
use App\Models\BranchRoute;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\{Select, TextInput, TimePicker, Toggle, Grid, Hidden, View};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class BranchRouteResource extends Resource
{
    protected static ?string $model = BranchRoute::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = "Transport";
    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                View::make('filament.components.branch-route-script'),

                Grid::make(2)->schema([
                    View::make('filament.branches.map-view')
                        ->label('اختر فرع الانطلاق من الخريطة')
                        ->extraAttributes(['data-branch-select' => 'from_branch_id'])
                        ->viewData([
                            'branchesForMap' => Branch::select('id', 'branch_name', 'latitude', 'longitude', 'email')->get()->map(function ($branch) {
                                return [
                                    'id' => $branch->id,
                                    'lat' => $branch->latitude,
                                    'lng' => $branch->longitude,
                                    'name' => $branch->branch_name,
                                    'email' => $branch->email,
                                ];
                            })->toArray(),
                            'centerLatitude' => 33.5138,
                            'centerLongitude' => 36.2765,
                            'zoomLevel' => 6,
                        ]),
                    View::make('filament.branches.map-view')
                        ->label('اختر فرع الوصول من الخريطة')
                        ->extraAttributes(['data-branch-select' => 'to_branch_id'])
                        ->viewData([
                            'branchesForMap' => Branch::select('id', 'branch_name', 'latitude', 'longitude', 'email')
                                ->get()
                                ->map(function ($branch) {
                                    return [
                                        'id' => $branch->id,
                                        'lat' => $branch->latitude,
                                        'lng' => $branch->longitude,
                                        'name' => $branch->branch_name,
                                        'email' => $branch->email,
                                    ];
                                })->toArray(),
                            'centerLatitude' => 33.5138,
                            'centerLongitude' => 36.2765,
                            'zoomLevel' => 6,
                        ]),
                ]),
                Grid::make(2)->schema([
                    Select::make('from_branch_id')
                        ->options(function () {
                            return Branch::select('id', 'branch_name')
                                ->get()
                                ->mapWithKeys(
                                    function ($branch) {
                                        return [$branch->id => $branch->branch_name];
                                    }
                                );
                        })
                        ->required(),
                    Select::make('to_branch_id')
                        ->options(function () {
                            return Branch::select('id', 'branch_name')
                                ->get()
                                ->mapWithKeys(
                                    function ($branch) {
                                        return [$branch->id => $branch->branch_name];
                                    }
                                );
                        })
                        ->required(),
                ]),

                Grid::make(2)->schema(
                    [

                        TextInput::make('distance_per_kilo')
                            ->numeric()
                            ->suffix('KM')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {})
                            ->columnSpan(1), // half width
                        Toggle::make('is_active')
                            ->label('?... is Active')
                            ->onIcon('')
                            ->offIcon('')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(1)
                            ->columnSpan(1),
                    ]
                ),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fromBranch.branch_name')
                    ->label('From Branch')
                    ->sortable(),
                TextColumn::make('toBranch.branch_name')
                    ->label('From Branch')
                    ->numeric()
                    ->sortable(),
                ActiveToggleColumn::make('is_active'),
                TextColumn::make('distance_per_kilo')
                    ->numeric()
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    // MarkAsArrivedAction::make(),
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
            'index' => Pages\ListBranchRoutes::route('/'),
            'create' => Pages\CreateBranchRoute::route('/create'),
            'edit' => Pages\EditBranchRoute::route('/{record}/edit'),
        ];
    }
}
