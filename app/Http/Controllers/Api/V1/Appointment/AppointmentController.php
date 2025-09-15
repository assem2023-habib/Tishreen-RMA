<?php

namespace App\Http\Controllers\Api\V1\Appointment;

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
            ->whereNull('user_id')
            ->where('booked', false)
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
        if ($appointment->booked || $appointment->user_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'This appointment is already booked.',
            ], 400);
        }

        // Assign appointment to the user
        $appointment->user_id = $request->user_id;
        $appointment->booked = true;
        $appointment->save();

        // Link appointment to parcel
        $parcel->appointment_id = $appointment->id;
        $parcel->save();

        return response()->json([
            'success' => true,
            'message' => 'Appointment successfully booked.',
            'appointment' => $appointment,
        ]);
    }
}
