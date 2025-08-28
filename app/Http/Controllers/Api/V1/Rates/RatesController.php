<?php

namespace App\Http\Controllers\Api\V1\Rates;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Rate\StoreRateRequest;
use App\Http\Requests\Api\V1\rate\UpdateRateRequest;
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
    public function store(StoreRateRequest $rate)
    {
        $data = $rate->validated();
        $rate = $this->ratesService->createRate($data);
        if (empty($rate))
            $this->errorResponse(
                __('rate.cannot_create_rate'),
                HttpStatus::INTERNET_SERVER_ERROR->value
            );
        return $this->successResponse(
            ['rate' => $rate],
            __('rate.rate_created_successfully'),
            HttpStatus::CREATED->value
        );
    }
    public function show($id)
    {
        $rate = $this->ratesService->getRate($id);
        if (empty($rate))
            return $this->errorResponse(
                __('rate.not_found'), // رسالة خطأ عند عدم وجود التقييم
                HttpStatus::NOT_FOUND->value
            );
        return $this->successResponse(
            ['rate' => $rate],
            __('rate.retrieved_successfully')
        );
    }
    public function update($id, UpdateRateRequest $request)
    {
        $data = $request->validated();
        $rate = $this->ratesService->updateRate($id, $data);
        if (empty($rate))
            return $this->errorResponse(
                __('rate.cannot_update_rate'),
                HttpStatus::BAD_REQUEST->value
            );
        return $this->successResponse(
            ['rate' => $rate],
            __('rate.rate_updated_successfully')
        );
    }
    public function destroy($id)
    {
        $deleted = $this->ratesService->deleteRate($id);

        if (!$deleted) {
            return $this->errorResponse(
                __('rate.cannot_delete_rate'),
                HttpStatus::NOT_FOUND->value
            );
        }

        return $this->successResponse(
            [],
            __('rate.rate_deleted_successfully')
        );
    }
}
