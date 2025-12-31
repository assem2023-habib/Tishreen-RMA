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
use App\Enums\SenderType;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $userId = Auth::id();

        $stats = [
            'parcels' => [
                'total' => Parcel::where('sender_id', $userId)
                    ->where('sender_type', SenderType::AUTHENTICATED_USER->value)
                    ->count(),
                'by_status' => $this->getParcelsByStatus($userId),
            ],
            'rates_count' => Rate::where('user_id', $userId)->count(),
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
     * @param int $userId
     * @return array
     */
    private function getParcelsByStatus($userId)
    {
        $statuses = ParcelStatus::values();
        $counts = [];

        foreach ($statuses as $status) {
            $counts[$status] = Parcel::where('sender_id', $userId)
                ->where('sender_type', SenderType::AUTHENTICATED_USER->value)
                ->where('parcel_status', $status)
                ->count();
        }

        return $counts;
    }
}
