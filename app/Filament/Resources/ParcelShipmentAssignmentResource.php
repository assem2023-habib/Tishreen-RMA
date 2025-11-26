<?php

namespace App\Filament\Resources;

use App\Enums\ParcelStatus;
use App\Filament\Helpers\TableActions;
use App\Filament\Resources\ParcelShipmentAssignmentResource\Pages;
use App\Filament\Resources\ParcelShipmentAssignmentResource\RelationManagers;
use App\Models\Employee;
use App\Models\Shipment;
use App\Models\{ParcelShipmentAssignment, Parcel};
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\{Builder, SoftDeletingScope};

class ParcelShipmentAssignmentResource extends Resource
{
    protected static ?string $model = ParcelShipmentAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationGroup = "Parcels";
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('parcel_id')
                    ->label('Parcel')
                    ->options(function () {
                        return Parcel::with('sender')
                            ->whereIn('parcel_status', [ParcelStatus::CONFIRMED->value])
                            ->get()
                            ->mapWithKeys(function ($parcel) {
                                return [$parcel->id => "Sender : " . $parcel->sender->name . ", " . " Reciver : " . $parcel->reciver_name . " ,Tracking Number : " . $parcel->tracking_number];
                            });
                    })
                    ->required(),
                Select::make('shipment_id')
                    ->options(function () {
                        return Shipment::with('branchRouteDay')
                            ->get()
                            ->mapWithKeys(
                                function ($shipment) {
                                    return [$shipment->id => $shipment->branchRouteDay->day_of_week . " ," . $shipment->branchRouteDay->branchRoute->route_label];
                                }
                            );
                    })
                    ->required(),
                Select::make('pick_up_confirmed_by_emp_id')
                    ->options(function () {
                        return Employee::with('user')
                            ->get()
                            ->mapWithKeys(function ($employee) {
                                return [$employee->id => $employee->user->user_name];
                            });
                    })
                    ->live()
                    ->label('Received By Employee'),
                DateTimePicker::make('pick_up_confirmed_date')
                    ->label('Received At')
                    ->default(now()),
                Select::make('delivery_confirmed_by_emp_id')
                    ->options(function (callable $get) {
                        $query = Employee::with('user');
                        if ($get('pick_up_confirmed_by_emp_id')) $query->whereNot('id', $get('pick_up_confirmed_by_emp_id'));
                        return $query
                            ->get()
                            ->mapWithKeys(function ($employee) {
                                return [$employee->id => $employee->user->user_name];
                            });
                    })
                    ->live()
                    ->label('Delivered By'),
                DateTimePicker::make('delivery_confirmed_date')
                    ->label('Delivered At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('parcel.sender_name')
                    ->label('Parcel'),
                TextColumn::make('shipment.branchRouteDay.branchRoute.route_label')
                    ->label('Shipment'),
                TextColumn::make('receivedByEmployee.user.user_name')
                    ->label('Received By'),
                TextColumn::make('pick_up_confirmed_date')
                    ->dateTime()
                    ->label('Received At'),
                TextColumn::make('deliveredByEmployee.user.user_name')
                    ->label('Delivered  By'),
                TextColumn::make('delivery_confirmed_date')
                    ->dateTime()
                    ->label('Delivered At'),
            ])
            ->filters([
                //
            ])
            ->actions([
                TableActions::default(),
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
            'index' => Pages\ListParcelShipmentAssignments::route('/'),
            'create' => Pages\CreateParcelShipmentAssignment::route('/create'),
            'edit' => Pages\EditParcelShipmentAssignment::route('/{record}/edit'),
        ];
    }
}
