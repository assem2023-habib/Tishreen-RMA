<?php

namespace App\Filament\Resources;

use App\Enums\SenderType;
use App\Filament\Resources\ParcelShipmentLogsResource\Pages;
use App\Filament\Resources\ParcelShipmentLogsResource\RelationManagers;
use App\Models\BranchRoute;
use App\Models\BranchRouteDays;
use App\Models\Employee;
use App\Models\GuestUser;
use App\Models\Parcel;
use App\Models\ParcelShipmentLogs;
use App\Models\Truck;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParcelShipmentLogsResource extends Resource
{
    // protected static ?string $model = ParcelShipmentLogs::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Parcels';
    protected static ?int $navigationSort = 3;

    protected static bool $shouldRegisterNavigation = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Grid::make()
                //     ->schema([
                //         Select::make('route_id')
                //             ->label('Routes')
                //             ->options(self::getBranchRoutes())
                //             // ->live()
                //             ->reactive()
                //             ->afterStateUpdated(fn(callable $set) => $set('day_id', null)),
                //         Select::make('day_id')
                //             ->label('Day')
                //             ->options(function (callable $get) {
                //                 $routeId = $get('route_id');
                //                 if (!$routeId) return [];
                //                 return BranchRouteDays::where('branch_route_id', $routeId)
                //                     ->get()
                //                     ->mapWithKeys(fn($day) => [$day->id => $day->day_of_week . " (" . $day->estimated_departur_time . ")"]);
                //             })
                //             ->reactive()
                //             ->afterStateUpdated(fn(callable $set) => $set('truck_id', null)),
                //         Select::make('truck_id')
                //             ->label('Truck')
                //             ->options(function (callable $get) {
                //                 $dayId = $get('day_id');
                //                 if (!$dayId) return [];
                //                 return Truck::WhereHas('branchRouteDays', fn($q) => $q->where('branch_route_days.id', $dayId))
                //                     ->with('driver.user')
                //                     ->get()
                //                     ->mapWithKeys(fn($truck) => [
                //                         $truck->id => $truck->driver->user->user_name
                //                             . ' | '
                //                             . $truck->truck_model
                //                             . ' | Capactiy: '
                //                             . $truck->capacity_per_kilo_gram
                //                             . 'kg'
                //                     ]);
                //             })
                //             ->reactive()
                //             ->required(),
                //     ]),
                // Grid::make()
                //     ->schema([
                //         Select::make('parcel_id')
                //             ->label('Parcel')
                //             ->options(
                //                 function (callable $get) {
                //                     $routeId = $get('route_id');
                //                     if (!$routeId) return [];

                //                     return Parcel::where('route_id', $routeId)
                //                         ->get()
                //                         ->mapWithKeys(function ($parcel) {
                //                             $sender = $parcel->sender_type === SenderType::AUTHENTICATED_USER
                //                                 ? optional(User::find($parcel->sender_id))->user_name
                //                                 : optional(GuestUser::find($parcel->sender_id))->first_name . ' ' . optional(GuestUser::find($parcel->sender_id))->last_name;

                //                             return [$parcel->id => "Parcel #{$parcel->id} - {$sender}"];
                //                         });
                //                 }
                //             )
                //             ->multiple()
                //             ->reactive()
                //             ->required(),
                //     ]),
                // Grid::make()
                //     ->schema([
                //         Select::make('pick_up_confirmed_by_emp_id')
                //             ->label('Emp Recive Parcel')
                //             ->options(function (callable $get) {
                //                 $routeId = $get('route_id');
                //                 $query = Employee::select('id', 'user_id', 'branch_id');
                //                 if ($routeId) {
                //                     $fromBranchId = BranchRoute::find($routeId);
                //                     if ($fromBranchId)
                //                         $query->where('branch_id', $fromBranchId->from_branch_id);
                //                 }
                //                 return $query
                //                     ->with('user')
                //                     ->get()
                //                     ->mapWithKeys(
                //                         function ($employee) {
                //                             return [$employee->id => optional($employee->user)->user_name];
                //                         }
                //                     );
                //             })
                //             ->searchable()
                //             ->reactive()
                //             ->required(),
                //         Select::make('delivery_confirmed_by_emp_id')
                //             ->label('Emp Delivered Parcel')
                //             ->options(function (callable $get) {
                //                 $routeId = $get('route_id');
                //                 if (!$routeId)
                //                     $routeId = [];
                //                 $query = Employee::select('id', 'user_id', 'branch_id');
                //                 if ($routeId) {
                //                     $toBranchId = BranchRoute::find($routeId);
                //                     if ($toBranchId)
                //                         $query->where('branch_id', $toBranchId->to_branch_id);
                //                 }
                //                 return $query
                //                     ->with('user')
                //                     ->get()
                //                     ->mapWithKeys(
                //                         function ($employee) {
                //                             return [$employee->id => optional($employee->user)->user_name];
                //                         }
                //                     );
                //             })
                //             ->searchable()
                //             ->reactive(),
                //     ]),

                // Select::make('truck_id')
                //     ->label('Truck')
                //     ->options(
                //         function (callable $get) {
                //             $route = $get('route_id');
                //             if (!$route)
                //                 $route = [];
                //             $trucks = Truck::select('id', 'driver_id', 'truck_model');
                //             if ($route) {
                //                 $branchRouteId = BranchRoute::find($route);
                //                 if ($branchRouteId) {
                //                     $trucksIds = $branchRouteId->trucks->pluck('id');
                //                     $trucks->whereIn('id', $trucksIds);
                //                 }
                //             }
                //             return
                //                 $trucks
                //                 ->with('driver.user')
                //                 ->get()
                //                 ->mapWithKeys(
                //                     function ($truck) {
                //                         return [$truck->id => $truck->driver->user->user_name . ', truck model: ' . $truck->truck_model];
                //                     }
                //                 );
                //         }
                //     ),
                // Grid::make(3)
                //     ->schema([
                //         DateTimePicker::make('pick_up_confiremd_date')
                //             ->required(),
                //         DateTimePicker::make('delivery_confirmed_date'),
                //         DateTimePicker::make('assigned_truck_date'),
                //     ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('parcel_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pick_up_confirmed_by_emp_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_confirmed_by_emp_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('truck_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pick_up_confiremd_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_confirmed_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assigned_truck_date')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListParcelShipmentLogs::route('/'),
            'create' => Pages\CreateParcelShipmentLogs::route('/create'),
            'edit' => Pages\EditParcelShipmentLogs::route('/{record}/edit'),
        ];
    }
    private static function getEmployees($branchId) {}
    private static function getBranchRoutes()
    {
        return BranchRoute::select('id', 'from_branch_id', 'to_branch_id')
            ->get()
            ->mapWithKeys(function ($route) {
                return [$route->id => $route->fromBranch->branch_name . '-->' . $route->toBranch->branch_name];
            });
    }
}
