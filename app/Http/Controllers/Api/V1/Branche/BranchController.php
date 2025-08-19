<?php

namespace App\Http\Controllers\Api\V1\Branche;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    use ApiResponse;
    public function __invoke()
    {
        try {
            $branches = Branch::select('id', 'address', 'phone', 'email', 'latitude', 'longitude', 'city_id')->get();
            if ($branches->isEmpty())
                return $this->errorResponse(
                    __('location.no.branches.found'),
                    HttpStatus::NOT_FOUND->value,
                    [],
                );
            return $this->successResponse(
                ['branches' => $branches],
                'branches get successfully',
            );
        } catch (\Throwable $e) {
            Log::error('Get Countries Error : ' . $e->getMessage(), ['tracr' => $e->getTraceAsString()]);
            return $this->errorResponse(
                message: __('location.server.error'),
                errors: ['exception' => $e->getMessage()],
            );
        }
    }

}
