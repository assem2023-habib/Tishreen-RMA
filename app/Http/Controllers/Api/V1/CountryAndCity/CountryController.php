<?php

namespace App\Http\Controllers\Api\V1\CountryAndCity;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Trait\ApiResponse;
use Illuminate\Support\Facades\Log;

class CountryController extends Controller
{
    use ApiResponse;

    // inject the Service obj to call the function

    public function getCountries()
    {
        try {
            $countries = Country::select('id', 'en_name', 'ar_name')->get();

            if ($countries->isEmpty()) {
                return $this->errorResponse(
                    __('location.no_countries_found'),
                    404,
                    []
                );
            }

            return $this->successResponse(
                ['countries' => $countries],
                __('location.countries_retrieved_successfully')
            );
        } catch (\Throwable $e) {
            Log::error('GetCountries Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return $this->errorResponse(
                __('location.server_error'),
                500,
                ['exception' => $e->getMessage()]
            );
        }
    }

    public function getCitiesByCountry($country_id)
    {
        try {
            $country = Country::find($country_id);

            if (!$country) {
                return $this->errorResponse(
                    __('location.country_not_found'),
                    404,
                    ['country_id' => __('location.invalid_country_id')]
                );
            }

            $cities = City::where('country_id', $country_id)
                ->select('id', 'en_name', 'ar_name')
                ->get();

            if ($cities->isEmpty()) {
                return $this->errorResponse(
                    __('location.no_cities_found'),
                    404,
                    []
                );
            }

            return $this->successResponse(
                ['cities' => $cities],
                __('location.cities_retrieved_successfully')
            );
        } catch (\Throwable $e) {
            Log::error('GetCities Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return $this->errorResponse(
                __('location.server_error'),
                500,
                ['exception' => $e->getMessage()]
            );
        }
    }
}
