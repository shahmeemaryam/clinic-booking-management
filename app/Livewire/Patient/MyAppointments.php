<?php

namespace App\Livewire\Patient;

use App\Models\Appointment;
use App\Services\AppointmentBookingService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class MyAppointments extends Component
{
    public function render()
    {
        $appointments = Appointment::query()
            ->where('patient_id', Auth::id())
            ->with([
                'doctorAvailability.doctor.user',
                'doctorAvailability.doctor.specialty',
                'payments',
            ])
            ->orderByDesc('created_at')
            ->get();

        $bookingService = app(AppointmentBookingService::class);

        foreach ($appointments as $appointment) {
            $appointment->latestPayment =
                $appointment->payments
                    ->sortByDesc('created_at')
                    ->first();

            $appointment->estimatedTime = null;

            if (
                in_array($appointment->status, [
                    'confirmed',
                    'in_progress',
                ], true)
                && $appointment->queue_number
            ) {
                $appointment->estimatedTime =
                    $bookingService->estimatedConsultationTime(
                        $appointment
                    );
            }
        }

        return view('livewire.patient.my-appointments', [
            'appointments' => $appointments,
        ]);
    }
public function cancelAppointment(int $appointmentId): void
{
    $appointment = \App\Models\Appointment::with('doctorAvailability')
        ->where('id', $appointmentId)
        ->where('patient_id', \Illuminate\Support\Facades\Auth::id())
        ->firstOrFail();

    // Only future confirmed appointments can be cancelled.
    if (
        $appointment->status !== 'confirmed' ||
        ! $appointment->doctorAvailability ||
        $appointment->doctorAvailability->date->isPast()
    ) {
        session()->flash(
            'error',
            'This appointment can no longer be cancelled.'
        );

        return;
    }

    $appointment->update([
        'status' => 'cancelled',
    ]);

    session()->flash(
        'success',
        'Your appointment has been cancelled successfully. Your payment will be reversed shortly.'
    );
}
    
}