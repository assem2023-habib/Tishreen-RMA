<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SuperAdminService;
use App\Trait\ApiResponse;
use App\Enums\HttpStatus;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    use ApiResponse;

    public function __construct(private SuperAdminService $superAdminService) {}

    public function stats()
    {
        $stats = $this->superAdminService->getSystemStats();
        return $this->successResponse(['stats' => $stats], 'System stats retrieved');
    }

    public function branches()
    {
        $branches = $this->superAdminService->listAllBranches();
        return $this->successResponse(['branches' => $branches], 'All branches retrieved');
    }

    public function storeBranch(Request $request)
    {
        $request->validate([
            'branch_name' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        $branch = $this->superAdminService->createBranch($request->all());
        return $this->successResponse(['branch' => $branch], 'Branch created successfully');
    }

    public function employees()
    {
        $employees = $this->superAdminService->listAllEmployees();
        return $this->successResponse(['employees' => $employees], 'All employees retrieved');
    }

    public function assignEmployee(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $employee = $this->superAdminService->assignEmployeeToBranch($request->user_id, $request->branch_id);
        return $this->successResponse(['employee' => $employee], 'Employee assigned to branch successfully');
    }

    public function allParcels()
    {
        $parcels = $this->superAdminService->getGlobalParcels();
        return $this->successResponse(['parcels' => $parcels], 'Global parcels retrieved');
    }
}
