<?php

namespace App\Http\Controllers\Api\V1\BranchRoute;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Models\BranchRoute;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class BranchRouteController extends Controller
{
    use ApiResponse;
    public function __invoke()
    {
        //         from_branch_id
        // to_branch_id
        // day
        // is_active
        // estimated_departur_time
        // estimated_arrival_time
        // distance_per_kilo
        $routes = BranchRoute::select(
            'from_branch_id',
            'to_branch_id',
            'day',
            'estimated_departur_time',
            'estimated_arrival_time',
            'distance_per_kilo'
        )
            ->where('is_active', 1)
            ->get();
        if (empty($routes))
            return $this->errorResponse(
                'No routes found',
                HttpStatus::NOT_FOUND->value,
            );
        return  $this->successResponse(
            ['routes' => $routes],
            'all routes get successfully'
        );
    }
    public function showRoutesByDay($day)
    {
        $routes = BranchRoute::select(
            'from_branch_id',
            'to_branch_id',
            'day',
            'estimated_departur_time',
            'estimated_arrival_time',
            'distance_per_kilo'
        )
            ->where('is_active', 1)
            ->where('day', $day)
            ->get();
        if (empty($routes))
            return $this->errorResponse(
                'No routes found for this day',
                HttpStatus::NOT_FOUND->value,
            );
        // $routes['from_branch'] = Branch::select('');
        return  $this->successResponse(
            ['routes' => $routes],
            'all routes get successfully for this day'
        );
    }
}
