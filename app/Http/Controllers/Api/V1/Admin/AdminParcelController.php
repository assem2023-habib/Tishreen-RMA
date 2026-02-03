<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminParcelService;
use App\Trait\ApiResponse;
use App\Enums\HttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;

class AdminParcelController extends Controller
{
    use ApiResponse;

    public function __construct(private AdminParcelService $adminParcelService) {}

    public function myBranch()
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            return $this->errorResponse('User is not an employee', HttpStatus::FORBIDDEN->value);
        }

        $branch = Branch::with(['city.country', 'routesFrom', 'routesTo'])->findOrFail($employee->branch_id);

        return $this->successResponse(['branch' => $branch], 'Branch details retrieved');
    }

    public function index(Request $request)
    {
        // If employee, filter by their branch
        $branchId = null;
        if (Auth::user()->employee) {
            $branchId = Auth::user()->employee->branch_id;
        }

        $parcels = $this->adminParcelService->listAllParcels($branchId);

        return $this->successResponse(
            ['parcels' => $parcels],
            'All parcels retrieved successfully'
        );
    }

    public function history($id)
    {
        $history = $this->adminParcelService->getParcelHistory($id);
        return $this->successResponse(['history' => $history], 'Parcel history retrieved');
    }

    public function confirmReception($id)
    {
        $result = $this->adminParcelService->confirmParcelReception($id);

        if (!$result['status']) {
            return $this->errorResponse($result['message'], HttpStatus::BAD_REQUEST->value);
        }

        return $this->successResponse(
            ['parcel' => $result['parcel']],
            'Parcel reception confirmed at branch'
        );
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $result = $this->adminParcelService->updateStatus($id, $request->status);

        if (!$result['status']) {
            return $this->errorResponse($result['message'], HttpStatus::BAD_REQUEST->value);
        }

        return $this->successResponse(
            ['parcel' => $result['parcel']],
            'Parcel status updated successfully'
        );
    }
}
