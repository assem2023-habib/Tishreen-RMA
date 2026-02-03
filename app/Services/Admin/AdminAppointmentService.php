<?php

namespace App\Services\Admin;

use App\Models\Appointment;
use App\Enums\AppointmentStatus;

class AdminAppointmentService
{
    public function listBranchAppointments($branchId)
    {
        // Appointments where the parcel's route from_branch is this branch
        return Appointment::with(['user', 'parcel'])
            ->whereHas('parcel.route', function ($q) use ($branchId) {
                $q->where('from_branch_id', $branchId);
            })
            ->orderBy('booked_at', 'desc')
            ->paginate(15);
    }

    public function updateAppointmentStatus($id, $status)
    {
        $appointment = Appointment::findOrFail($id);
        
        // Basic validation could be added here
        $appointment->update([
            'status' => $status
        ]);

        return $appointment->fresh();
    }
}
