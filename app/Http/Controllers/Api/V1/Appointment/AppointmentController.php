<?php

namespace App\Http\Controllers\Api\V1\Appointment;

use App\Enums\AppointmentStatus;
use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\BranchRoute;
use App\Models\Parcel;
use App\Trait\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Get available appointments for a parcel by its tracking number
     */
    public function getCalenderByParcelTrackingNumber($tracking_number)
    {
        // Find the parcel by tracking number
        $parcel = Parcel::where('tracking_number', $tracking_number)->first();

        if (!$parcel) {
            return response()->json([
                'success' => false,
                'message' => 'Parcel not found.',
            ], 404);
        }

        // Get the route associated with the parcel
        $route = $parcel->route;

        if (!$route) {
            return response()->json([
                'success' => false,
                'message' => 'Route not assigned to this parcel.',
            ], 404);
        }

        // Minimum date: 1 day after the estimated arrival
        $minDate = Carbon::parse($route->estimated_arrival_time)->addDay()->toDateString();

        // Get all appointments for this route that are not booked and after minDate
        $availableAppointments = Appointment::query()
            ->where('branch_id', $route->to_branch_id)
            ->where('status', AppointmentStatus::AVAILABLE)
            ->whereDate('date', '>=', $minDate)
            ->get(); // now we fetch the collection

        return response()->json([
            'success' => true,
            'parcel' => $parcel,
            'available_appointments' => $availableAppointments,
        ]);
    }


    public function bookAppointment(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|exists:parcels,tracking_number',
            'appointment_id' => 'required|exists:appointments,id',
            'user_id' => 'required|exists:users,id',
        ]);

        // Find the parcel
        $parcel = Parcel::where('tracking_number', $request->tracking_number)->first();

        // Find the selected appointment
        $appointment = Appointment::findOrFail($request->appointment_id);

        // Check if already booked
        if ($appointment->status !== AppointmentStatus::AVAILABLE) {
            return response()->json([
                'success' => false,
                'message' => 'This appointment is no longer available.',
            ], 400);
        }

        // Assign appointment to the user as Pending
        $appointment->user_id = $request->user_id;
        $appointment->booked = true;
        $appointment->status = AppointmentStatus::PENDING;
        $appointment->booked_at = now();
        $appointment->save();

        // Link appointment to parcel
        $parcel->appointment_id = $appointment->id;
        $parcel->save();

        return response()->json([
            'success' => true,
            'message' => 'Appointment request submitted. You have 10 minutes to modify or cancel.',
            'appointment' => $appointment,
            'can_modify_until' => $appointment->booked_at->addMinutes(10)->toDateTimeString(),
        ]);
    }

    public function cancelAppointment(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $appointment = Appointment::where('id', $request->appointment_id)
            ->where('user_id', $request->user_id)
            ->firstOrFail();

        // Check if within 10 minutes
        if ($appointment->status === AppointmentStatus::PENDING && $appointment->booked_at->diffInMinutes(now()) > 10) {
            return response()->json([
                'success' => false,
                'message' => 'The 10-minute modification period has passed. You cannot cancel this appointment now.',
            ], 400);
        }

        // Reset appointment
        $appointment->status = AppointmentStatus::AVAILABLE;
        $appointment->user_id = null;
        $appointment->booked = false;
        $appointment->booked_at = null;
        $appointment->save();

        // Remove link from parcel
        Parcel::where('appointment_id', $appointment->id)->update(['appointment_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment successfully cancelled.',
        ]);
    }

    public function updateAppointment(Request $request)
    {
        $request->validate([
            'old_appointment_id' => 'required|exists:appointments,id',
            'new_appointment_id' => 'required|exists:appointments,id',
            'user_id' => 'required|exists:users,id',
            'tracking_number' => 'required|exists:parcels,tracking_number',
        ]);

        $oldAppointment = Appointment::where('id', $request->old_appointment_id)
            ->where('user_id', $request->user_id)
            ->firstOrFail();

        // Check if within 10 minutes
        if ($oldAppointment->status === AppointmentStatus::PENDING && $oldAppointment->booked_at->diffInMinutes(now()) > 10) {
            return response()->json([
                'success' => false,
                'message' => 'The 10-minute modification period has passed. You cannot change this appointment now.',
            ], 400);
        }

        $newAppointment = Appointment::findOrFail($request->new_appointment_id);
        if ($newAppointment->status !== AppointmentStatus::AVAILABLE) {
            return response()->json([
                'success' => false,
                'message' => 'The new appointment slot is not available.',
            ], 400);
        }

        // Reset old appointment
        $oldAppointment->status = AppointmentStatus::AVAILABLE;
        $oldAppointment->user_id = null;
        $oldAppointment->booked = false;
        $oldAppointment->booked_at = null;
        $oldAppointment->save();

        // Set new appointment
        $newAppointment->user_id = $request->user_id;
        $newAppointment->booked = true;
        $newAppointment->status = AppointmentStatus::PENDING;
        $newAppointment->booked_at = now();
        $newAppointment->save();

        // Update parcel link
        $parcel = Parcel::where('tracking_number', $request->tracking_number)->first();
        $parcel->appointment_id = $newAppointment->id;
        $parcel->save();

        return response()->json([
            'success' => true,
            'message' => 'Appointment successfully updated. You have 10 minutes to modify or cancel.',
            'appointment' => $newAppointment,
            'can_modify_until' => $newAppointment->booked_at->addMinutes(10)->toDateTimeString(),
        ]);
    }
}
