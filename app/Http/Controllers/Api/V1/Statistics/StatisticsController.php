<?php

namespace App\Http\Controllers\Api\V1\Statistics;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\City;
use App\Models\Country;
use App\Models\Parcel;
use App\Models\Rate;
use App\Models\Shipment;
use App\Models\Truck;
use App\Models\User;
use App\Enums\ParcelStatus;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    use ApiResponse;

    /**
     * Get general statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'parcels' => [
                'total' => Parcel::count(),
                'by_status' => $this->getParcelsByStatus(),
            ],
            'rates_count' => Rate::count(),
            'branches_count' => Branch::count(),
            'shipments_count' => Shipment::count(),
            'trucks_count' => Truck::count(),
            'locations' => [
                'countries_count' => Country::count(),
                'cities_count' => City::count(),
            ],
        ];

        return $this->successResponse($stats, 'Statistics retrieved successfully');
    }

    /**
     * Get parcels count grouped by status.
     *
     * @return array
     */
    private function getParcelsByStatus()
    {
        $statuses = ParcelStatus::values();
        $counts = [];

        foreach ($statuses as $status) {
            $counts[$status] = Parcel::where('parcel_status', $status)->count();
        }

        return $counts;
    }
}
