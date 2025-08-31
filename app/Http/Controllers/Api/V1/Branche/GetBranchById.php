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
        $branch = Branch::select(
            'id',
            'address',
            'phone',
            'email',
            'latitude',
            'longitude',
            'city_id'
        )
            ->where('id', $id)
            ->first();
        if (empty($branch))
            return $this->errorResponse(
                'No branch found for this id',
                HttpStatus::NOT_FOUND->value,
            );
        $city = City::select(
            'id',
            'country_id',
            'ar_name',
            'en_name'
        )
            ->where('id', $branch->city_id)
            ->first();
        $branch->city = $city;
        unset($branch->city_id);
        return $this->successResponse(
            ['branch' => $branch],
            'get Branch by id successfuly'
        );
    }
}
