<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminAppointmentService;
use App\Trait\ApiResponse;
use App\Enums\HttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAppointmentController extends Controller
{
    use ApiResponse;

    public function __construct(private AdminAppointmentService $adminAppointmentService) {}

    public function index()
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            return $this->errorResponse('User is not an employee', HttpStatus::FORBIDDEN->value);
        }

        $appointments = $this->adminAppointmentService->listBranchAppointments($employee->branch_id);

        return $this->successResponse(
            ['appointments' => $appointments],
            'Branch appointments retrieved successfully'
        );
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $appointment = $this->adminAppointmentService->updateAppointmentStatus($id, $request->status);

        return $this->successResponse(
            ['appointment' => $appointment],
            'Appointment status updated successfully'
        );
    }
}
