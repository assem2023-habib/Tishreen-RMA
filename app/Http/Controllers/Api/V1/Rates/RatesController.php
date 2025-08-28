<?php

namespace App\Http\Controllers\Api\V1\Rates;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Services\RatesService;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class RatesController extends Controller
{
    use ApiResponse;
    public function __construct(private RatesService $ratesService) {}
    public function index()
    {
        $rates = $this->ratesService->getRates();
        if (empty($rates))
            return $this->errorResponse(
                __('rate.no_rates_found'),
                HttpStatus::NOT_FOUND->value
            );
        return $this->successResponse(
            ['rates' => $rates],
            __('rate.rates_retrieved_successfully')
        );
    }
}
