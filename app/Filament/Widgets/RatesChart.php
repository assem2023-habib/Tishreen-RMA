<?php

namespace App\Filament\Widgets;

use App\Models\Rate;
use Filament\Widgets\ChartWidget;

class RatesChart extends ChartWidget
{
    protected static ?string $heading = 'Rates Analysis';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $ratings = Rate::selectRaw('rating, Count(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();
        $labels = [1, 2, 3, 4, 5];
        $data = array_map(fn($rating) => $ratings[$rating] ?? 0, $labels);
        return [
            'datasets' => [
                [
                    'label' => 'number of rates : ',
                    'data' => $data,
                    'backgroundColor' => [
                        '#ef4444',
                        '#f97316',
                        '#eab308',
                        '#22c55e',
                        '#3b82f6',
                    ],
                ],
            ],
            'labels' => array_map(fn($star) => "$star ⭐", $labels)
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
