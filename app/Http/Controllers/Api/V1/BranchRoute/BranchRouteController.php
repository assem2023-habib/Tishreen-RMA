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
        $routes = BranchRoute::with(['days', 'fromBranch', 'toBranch'])
            ->where('is_active', 1)
            ->get();

        if ($routes->isEmpty())
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
        $routes = BranchRoute::whereHas('days', function ($query) use ($day) {
            $query->where('day_of_week', $day);
        })
            ->with([
                'days' => function ($query) use ($day) {
                    $query->where('day_of_week', $day);
                },
                'fromBranch',
                'toBranch'
            ])
            ->where('is_active', 1)
            ->get();

        if ($routes->isEmpty())
            return $this->errorResponse(
                'No routes found for this day',
                HttpStatus::NOT_FOUND->value,
            );

        return  $this->successResponse(
            ['routes' => $routes],
            'all routes get successfully for this day'
        );
    }
}
