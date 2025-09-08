<?php

namespace App\Http\Controllers\Api\V1\Branche;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class BranchByCityController extends Controller
{
    use ApiResponse;
    public function __invoke($cityId)
    {
        $branches = Branch::all()
            ->where('city_id', $cityId);

        if (!$branches->isEmpty())
            return $this->successResponse(
                ['branches' => $branches->fresh()],
                message: "Get all branches by city $cityId",
            );

        return $this->errorResponse(
            'error',
        );
    }
}
