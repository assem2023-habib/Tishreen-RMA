<?php

namespace App\Filament\Resources\BranchRouteResource\Pages;

use App\Filament\Resources\BranchRouteResource;
use App\Models\Branch;
use Filament\Actions;
use App\Services\AppointmentHepler;
use Filament\Resources\Pages\CreateRecord;

class CreateBranchRoute extends CreateRecord
{
    protected static string $resource = BranchRouteResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $branch1 = Branch::find($data['from_branch_id']);
        $branch2 = Branch::find($data['to_branch_id']);

        if ($branch1 && $branch2) {
            $data['distance_per_kilo'] = $this->calculateDistance(
                $branch1->lat,
                $branch1->lng,
                $branch2->lat,
                $branch2->lng
            );
        }


        return $data;
    }


    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}
