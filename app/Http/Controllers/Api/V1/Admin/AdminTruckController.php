<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminTruckService;
use App\Trait\ApiResponse;
use App\Enums\HttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTruckController extends Controller
{
    use ApiResponse;

    public function __construct(private AdminTruckService $adminTruckService) {}

    public function index()
    {
        $branchId = null;
        if (Auth::user()->employee) {
            $branchId = Auth::user()->employee->branch_id;
        }

        $trucks = $this->adminTruckService->listTrucks($branchId);

        return $this->successResponse(
            ['trucks' => $trucks],
            'Trucks retrieved successfully'
        );
    }

    public function show($id)
    {
        $truck = $this->adminTruckService->getTruckDetails($id);
        return $this->successResponse(['truck' => $truck], 'Truck details retrieved');
    }

    public function toggleStatus($id)
    {
        $truck = $this->adminTruckService->toggleTruckStatus($id);
        return $this->successResponse(['truck' => $truck], 'Truck status toggled');
    }
}
