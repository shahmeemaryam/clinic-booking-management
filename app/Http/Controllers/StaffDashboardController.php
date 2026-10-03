<?php

namespace App\Http\Controllers;

use App\Models\DoctorAvailability;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = today();

        $clinics = DoctorAvailability::query()
            ->with([
                'doctor.user',
            ])
            ->withCount([
                'appointments as booked_count' => function ($query) {
                    $query->whereIn('status', [
                        'confirmed',
                        'arrived',
                        'in_progress',
                        'completed',
                    ]);
                },

                'appointments as arrived_count' => function ($query) {
                    $query->where('status', 'arrived');
                },

                'appointments as in_progress_count' => function ($query) {
                    $query->where('status', 'in_progress');
                },

                'appointments as completed_count' => function ($query) {
                    $query->where('status', 'completed');
                },
            ])
            ->whereDate('date', $today)
            ->orderBy('start_time')
            ->get();

        return view('staff.dashboard', [
            'clinics' => $clinics,
            'today' => $today,
        ]);
    }
}