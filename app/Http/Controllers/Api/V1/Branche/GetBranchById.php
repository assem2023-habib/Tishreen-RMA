<?php

namespace App\Http\Controllers\Api\V1\Branche;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\City;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class GetBranchById extends Controller
{
    use ApiResponse;
    public function __invoke($id)
    {
        /**
         * updated to Eager Loading 
         * 2 by 1 get brach and inject city with it 
         * Author: Hussein Kurhaily
         * Updated : 2025-09-07
         */
        $branch = Branch::with('city:id,country_id,ar_name,en_name')->find($id);

        if (empty($branch))
            return $this->errorResponse(
                'No branch found for this id',
                HttpStatus::NOT_FOUND->value,
            );



        return $this->successResponse(
            ['branch' => $branch],
            'get Branch by id successfuly'
        );
    }
}
