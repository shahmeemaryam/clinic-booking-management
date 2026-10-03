<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorAvailability;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentBookingService
{

public function confirmPayment(
    int $paymentId,
    string $transactionReference,
    array $gatewayResponse = []
): Appointment {
    return DB::transaction(function () use (
        $paymentId,
        $transactionReference,
        $gatewayResponse
    ) {
        $payment = \App\Models\Payment::query()
            ->with('appointment.doctorAvailability')
            ->whereKey($paymentId)
            ->lockForUpdate()
            ->firstOrFail();

        if ($payment->status === 'paid') {
            return $payment->appointment;
        }

        if ($payment->status !== 'pending') {
            throw ValidationException::withMessages([
                'payment' => 'This payment can no longer be completed.',
            ]);
        }

        if ($payment->expires_at && $payment->expires_at->isPast()) {
            $payment->update([
                'status' => 'expired',
            ]);

            throw ValidationException::withMessages([
                'payment' => 'The payment window has expired.',
            ]);
        }

        $appointment = $payment->appointment;

        $availability = DoctorAvailability::query()
            ->whereKey($appointment->doctor_availability_id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($appointment->status !== 'pending_payment') {
            throw ValidationException::withMessages([
                'payment' => 'This appointment is no longer awaiting payment.',
            ]);
        }

        $nextQueueNumber = (
            Appointment::query()
                ->where('doctor_availability_id', $availability->id)
                ->max('queue_number')
            ?? 0
        ) + 1;

        $payment->update([
            'status' => 'paid',
            'transaction_reference' => $transactionReference,
            'paid_at' => now(),
            'gateway_response' => $gatewayResponse,
        ]);

        $appointment->update([
            'status' => 'confirmed',
            'queue_number' => $nextQueueNumber,
        ]);

        return $appointment->fresh();
    });
}
    public function book(
    int $patientId,
    int $availabilityId,
    ?string $reason = null
): Appointment {
    return DB::transaction(function () use (
        $patientId,
        $availabilityId,
        $reason
    ) {
        $availability = DoctorAvailability::query()
            ->whereKey($availabilityId)
            ->lockForUpdate()
            ->firstOrFail();

        $now = now();

        $activeStatuses = [
    'confirmed',
    'arrived',
    'in_progress',
];

        $activeBookingCount = Appointment::query()
            ->where('doctor_availability_id', $availability->id)
            ->where(function ($query) use ($activeStatuses, $now) {
                $query
                    ->whereIn('status', $activeStatuses)
                    ->orWhere(function ($pending) use ($now) {
                        $pending
                            ->where('status', 'pending_payment')
                            ->whereHas('payments', function ($payment) use ($now) {
                                $payment
                                    ->where('status', 'pending')
                                    ->where('expires_at', '>', $now);
                            });
                    });
            })
            ->count();

        if ($activeBookingCount >= $availability->capacity) {
            throw ValidationException::withMessages([
                'appointment' => 'This clinic session is fully booked.',
            ]);
        }

        $alreadyBooked = Appointment::query()
            ->where('doctor_availability_id', $availability->id)
            ->where('patient_id', $patientId)
            ->where(function ($query) use ($activeStatuses, $now) {
                $query
                    ->whereIn('status', $activeStatuses)
                    ->orWhere(function ($pending) use ($now) {
                        $pending
                            ->where('status', 'pending_payment')
                            ->whereHas('payments', function ($payment) use ($now) {
                                $payment
                                    ->where('status', 'pending')
                                    ->where('expires_at', '>', $now);
                            });
                    });
            })
            ->exists();

        if ($alreadyBooked) {
            throw ValidationException::withMessages([
                'appointment' => 'You already have an active booking for this clinic session.',
            ]);
        }

        $appointment = Appointment::create([
            'patient_id' => $patientId,
            'doctor_availability_id' => $availability->id,
            'queue_number' => null,
            'reason' => $reason,
            'status' => 'pending_payment',
        ]);

        $appointment->payments()->create([
            'patient_id' => $patientId,
            'gateway' => config('services.payment.gateway', 'sandbox'),
            'amount' => $availability->consultation_fee,
            'currency' => 'LKR',
            'status' => 'pending',
            'expires_at' => $now->copy()->addMinutes(10),
        ]);

        return $appointment->fresh();
    });
}

    public function estimatedConsultationTime(Appointment $appointment): Carbon
{
    $availability = $appointment->doctorAvailability;

    $start = Carbon::parse(
        $availability->date->format('Y-m-d') . ' ' . $availability->start_time
    );

    $activeStatuses = [
    'pending_payment',
    'confirmed',
    'in_progress',
];

    $patientsAhead = Appointment::query()
        ->where('doctor_availability_id', $availability->id)
        ->whereIn('status', $activeStatuses)
        ->where('queue_number', '<', $appointment->queue_number)
        ->count();

    $minutes = $patientsAhead
        * $availability->estimated_consultation_minutes;

    return $start->copy()->addMinutes($minutes);
}
}
