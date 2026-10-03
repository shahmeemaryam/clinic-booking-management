<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorWeeklySchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffScheduleController extends Controller
{
    public function index(): View
    {
        $doctors = Doctor::query()
            ->orderBy('name')
            ->get();

        $schedules = DoctorWeeklySchedule::query()
            ->with('doctor')
            ->orderBy('doctor_id')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('staff.schedules', [
            'doctors' => $doctors,
            'schedules' => $schedules,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'doctor_id' => [
                'required',
                'integer',
                'exists:doctors,id',
            ],

            'day_of_week' => [
                'required',
                'integer',
                'between:1,7',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],

            'capacity' => [
                'required',
                'integer',
                'min:1',
                'max:500',
            ],

            'estimated_consultation_minutes' => [
                'required',
                'integer',
                'min:1',
                'max:180',
            ],

            'consultation_fee' => [
                'required',
                'numeric',
                'min:0',
                'max:1000000',
            ],
        ]);

        $existing = DoctorWeeklySchedule::query()
            ->where('doctor_id', $validated['doctor_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('start_time', $validated['start_time'] . ':00')
            ->where('end_time', $validated['end_time'] . ':00')
            ->first();

        if ($existing) {
            if ($existing->active) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'schedule' => 'This doctor already has this clinic time.',
                    ]);
            }

            $existing->update([
                'capacity' => $validated['capacity'],
                'estimated_consultation_minutes' =>
                    $validated['estimated_consultation_minutes'],
                'consultation_fee' => $validated['consultation_fee'],
                'active' => true,
            ]);

            return redirect()
                ->route('staff.schedules')
                ->with('success', 'The clinic schedule has been reactivated.');
        }

        DoctorWeeklySchedule::create([
            'doctor_id' => $validated['doctor_id'],
            'day_of_week' => $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'capacity' => $validated['capacity'],
            'estimated_consultation_minutes' =>
                $validated['estimated_consultation_minutes'],
            'consultation_fee' => $validated['consultation_fee'],
            'active' => true,
        ]);

        return redirect()
            ->route('staff.schedules')
            ->with('success', 'Clinic schedule created successfully.');
    }

    public function update(
        Request $request,
        DoctorWeeklySchedule $schedule
    ): RedirectResponse {
        $validated = $request->validate([
            'day_of_week' => [
                'required',
                'integer',
                'between:1,7',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],

            'capacity' => [
                'required',
                'integer',
                'min:1',
                'max:500',
            ],

            'estimated_consultation_minutes' => [
                'required',
                'integer',
                'min:1',
                'max:180',
            ],

            'consultation_fee' => [
                'required',
                'numeric',
                'min:0',
                'max:1000000',
            ],
        ]);

        $duplicate = DoctorWeeklySchedule::query()
            ->where('doctor_id', $schedule->doctor_id)
            ->where('id', '!=', $schedule->id)
            ->where('day_of_week', $validated['day_of_week'])
            ->where('start_time', $validated['start_time'] . ':00')
            ->where('end_time', $validated['end_time'] . ':00')
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->withErrors([
                    'schedule' => 'Another clinic already uses this time for this doctor.',
                ]);
        }

        $schedule->update([
            'day_of_week' => $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'capacity' => $validated['capacity'],
            'estimated_consultation_minutes' =>
                $validated['estimated_consultation_minutes'],
            'consultation_fee' => $validated['consultation_fee'],
        ]);

        return redirect()
            ->route('staff.schedules')
            ->with('success', 'Clinic schedule updated successfully.');
    }

    public function destroy(DoctorWeeklySchedule $schedule)
{
    $schedule->delete();

    return back()->with(
        'success',
        'Clinic schedule deleted successfully.'
    );
}
}