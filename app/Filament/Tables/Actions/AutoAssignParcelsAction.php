<?php

namespace App\Filament\Tables\Actions;

use App\Enums\ParcelStatus;
use App\Models\Parcel;
use App\Models\Shipment;
use App\Models\ParcelShipmentAssignment;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class AutoAssignParcelsAction
{
    public static function make(): Action
    {
        return Action::make('autoAssignParcels')
            ->label('Auto Assign Parcels')
            ->icon('heroicon-o-sparkles')
            ->color('success')
            ->form([
                Select::make('shipment_id')
                    ->label('Target Shipment')
                    ->options(function () {
                        return Shipment::with(['branchRouteDay.branchRoute', 'trucks'])
                            ->get()
                            ->mapWithKeys(function ($shipment) {
                                $trucksCount = $shipment->trucks->count();
                                $totalCapacity = $shipment->trucks->sum('capacity_per_kilo_gram');
                                $route = $shipment->branchRouteDay->branchRoute->route_label;
                                $day = $shipment->branchRouteDay->day_of_week;

                                return [$shipment->id => "{$day} | {$route} ({$trucksCount} Trucks, {$totalCapacity}kg)"];
                            });
                    })
                    ->required(),
            ])
            ->action(function (array $data) {
                $shipment = Shipment::with(['trucks', 'branchRouteDay.branchRoute'])->findOrFail($data['shipment_id']);

                // Calculate total capacity from all trucks linked to this shipment
                $totalCapacity = $shipment->trucks->sum('capacity_per_kilo_gram');

                // Get the route of the shipment
                $routeId = $shipment->branchRouteDay->branch_route_id;

                // Find oldest CONFIRMED parcels that match this route and are not yet assigned
                // and whose cumulative weight is <= total capacity
                $parcels = Parcel::where('parcel_status', ParcelStatus::CONFIRMED)
                    ->where('route_id', $routeId)
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('parcel_shipment_assignments')
                            ->whereColumn('parcel_shipment_assignments.parcel_id', 'parcels.id');
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();

                $assignedCount = 0;
                $currentWeight = 0;
                $assignedParcelIds = [];

                foreach ($parcels as $parcel) {
                    if (($currentWeight + $parcel->weight) <= $totalCapacity) {
                        $currentWeight += $parcel->weight;
                        $assignedParcelIds[] = $parcel->id;
                        $assignedCount++;
                    } else {
                        // Capacity reached
                        break;
                    }
                }

                if ($assignedCount > 0) {
                    $user = auth()->user();
                    $employee = ($user instanceof \App\Models\User) ? $user->employee : null;

                    DB::transaction(function () use ($assignedParcelIds, $shipment, $employee) {
                        foreach ($assignedParcelIds as $parcelId) {
                            $data = [
                                'parcel_id' => $parcelId,
                                'shipment_id' => $shipment->id,
                                'pick_up_confirmed_date' => now(),
                            ];

                            if ($employee) {
                                $data['pick_up_confirmed_by_emp_id'] = $employee->id;
                            }

                            ParcelShipmentAssignment::create($data);

                            // Update parcel status to READY_FOR_SHIPPING
                            $parcel = Parcel::find($parcelId);
                            if ($parcel) {
                                $parcel->update([
                                    'parcel_status' => ParcelStatus::READY_FOR_SHIPPING,
                                ]);

                                // Send Dashboard Notification to Sender
                                $sender = $parcel->sender;
                                if ($sender instanceof \App\Models\User) {
                                    $trackingNumber = $parcel->tracking_number ?? '---';
                                    Notification::make()
                                        ->title('الطرد جاهز للشحن')
                                        ->body("تم ربط طردك ذو الرقم المرجعي ($trackingNumber) بشحنة وهو الآن جاهز للانطلاق.")
                                        ->success()
                                        ->icon('heroicon-o-truck')
                                        ->sendToDatabase($sender)
                                        ->broadcast($sender);
                                }
                            }
                        }
                    });

                    Notification::make()
                        ->title('Parcels Auto-Assigned')
                        ->body("Successfully assigned {$assignedCount} parcels (Total Weight: {$currentWeight}kg) to the shipment.")
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('No Parcels Assigned')
                        ->body('No eligible parcels found or trucks have zero capacity.')
                        ->warning()
                        ->send();
                }
            })
            ->requiresConfirmation();
    }
}
