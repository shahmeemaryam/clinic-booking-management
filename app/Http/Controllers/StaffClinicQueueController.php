<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorAvailability;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StaffClinicQueueController extends Controller
{

public function index(): View
{
    $clinics = DoctorAvailability::with([
        'doctor',
        'appointments',
    ])
        ->whereDate('date', '>=', now()->toDateString())
        ->orderBy('date')
        ->orderBy('start_time')
        ->get();

    return view('staff.clinic-queues', [
        'clinics' => $clinics,
    ]);
}

    public function show(DoctorAvailability $availability): View
    {
        $availability->load('doctor');

        $appointments = $availability->appointments()
            ->with('patient')
            ->whereIn('status', [
                'confirmed',
                'arrived',
                'in_progress',
                'completed',
            ])
            ->orderBy('queue_number')
            ->get();

        return view('staff.clinic-queue', [
            'availability' => $availability,
            'appointments' => $appointments,
        ]);
    }

    public function updateStatus(
        Appointment $appointment,
        string $status
    ): RedirectResponse {
        $allowedTransitions = [
            'confirmed' => ['arrived'],
            'arrived' => ['in_progress'],
            'in_progress' => ['completed'],
        ];

        $currentStatus = $appointment->status;

        if (
            ! isset($allowedTransitions[$currentStatus]) ||
            ! in_array($status, $allowedTransitions[$currentStatus], true)
        ) {
            return back()->with(
                'schedule',
                'This appointment cannot be moved to that status.'
            );
        }

        $appointment->update([
            'status' => $status,
        ]);

        return back()->with(
            'success',
            'Appointment status updated successfully.'
        );
    }
}