<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminShipmentService;
use App\Trait\ApiResponse;
use App\Enums\HttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminShipmentController extends Controller
{
    use ApiResponse;

    public function __construct(private AdminShipmentService $adminShipmentService) {}

    public function index(Request $request)
    {
        $branchId = null;
        if (Auth::user()->employee) {
            $branchId = Auth::user()->employee->branch_id;
        }

        $shipments = $this->adminShipmentService->listShipments($branchId);

        return $this->successResponse(
            ['shipments' => $shipments],
            'Shipments retrieved successfully'
        );
    }

    public function depart($id)
    {
        $result = $this->adminShipmentService->departShipment($id);

        if (!$result['status']) {
            return $this->errorResponse($result['message'], HttpStatus::BAD_REQUEST->value);
        }

        return $this->successResponse(
            ['shipment' => $result['shipment']],
            'Shipment marked as departed'
        );
    }

    public function arrive($id)
    {
        $result = $this->adminShipmentService->arriveShipment($id);

        if (!$result['status']) {
            return $this->errorResponse($result['message'], HttpStatus::BAD_REQUEST->value);
        }

        return $this->successResponse(
            ['shipment' => $result['shipment']],
            'Shipment marked as arrived'
        );
    }
}
