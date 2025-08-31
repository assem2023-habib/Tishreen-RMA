<?php

namespace App\Http\Controllers\Api\V1\Day;

use App\Enums\DaysOfWeek;
use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class DaysController extends Controller
{
    use ApiResponse;
    public function __invoke()
    {
        $days = DaysOfWeek::values();
        if (empty($days))
            return $this->errorResponse(
                'no days exists',
                HttpStatus::NOT_FOUND->value
            );
        return $this->successResponse(
            [
                'days' => $days
            ],
            'all days get successfully'
        );
    }
}
