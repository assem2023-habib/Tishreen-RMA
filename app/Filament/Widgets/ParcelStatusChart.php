<?php

namespace App\Filament\Widgets;

use App\Enums\ParcelStatus;
use App\Models\Parcel;
use Filament\Widgets\ChartWidget;

class ParcelStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        $statuses = ParcelStatus::values();
        $counts = [];

        foreach ($statuses as $status) {
            $counts[] = Parcel::where('parcel_status', $status)->count();
        }
        return [
            'datasets' => [
                [
                    'label' => 'Number of Parcels',
                    'data' => $counts,
                    'backgroundColor' => [
                        '#6b7280', // Pending
                        '#3b82f6', // Confirmed
                        '#38bdf8', // Ready_For_Shipping (Sky color)
                        '#22c55e', // In_transit
                        '#facc15', // Out_For_Delivery
                        '#a855f7', // Ready_For_Pickup
                        '#16a34a', // Delivered
                        '#ef4444', // Failed
                        '#f97316', // Returned
                        '#9ca3af', // Canceled
                    ],
                ],
            ],
            'labels' => $statuses,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
