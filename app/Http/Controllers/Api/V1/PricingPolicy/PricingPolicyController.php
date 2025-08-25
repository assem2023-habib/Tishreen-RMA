<?php

namespace App\Http\Controllers\Api\V1\PricingPolicy;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Models\PricingPolicy;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class PricingPolicyController extends Controller
{
    use ApiResponse;
    public function __invoke()
    {
        try {
            $pricingPolicy = PricingPolicy::select('id', 'policy_type', 'price', 'price_unit', 'limit_min', 'limit_max', 'currency')->where('is_active', 1)->get();
            if (!$pricingPolicy->isEmpty())
                return $this->successResponse(
                    $pricingPolicy,
                );
            return $this->errorResponse(
                'pricing policy not found !',
                HttpStatus::NOT_FOUND->value,
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
            );
        }
    }
}
