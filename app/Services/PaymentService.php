<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        private PaymentGateway $gateway
    ) {
    }

    public function initiatePayment(
        Appointment $appointment,
        array $customer = []
    ): array {
        $appointment->loadMissing('doctorAvailability', 'payments');

        if ($appointment->status !== 'pending_payment') {
            throw ValidationException::withMessages([
                'payment' => 'This appointment is not awaiting payment.',
            ]);
        }

        $payment = $appointment->payments()
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$payment) {
            throw ValidationException::withMessages([
                'payment' => 'No pending payment exists for this appointment.',
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

        $reference = 'APT-' . $appointment->id . '-' . now()->format('YmdHis');

        $result = $this->gateway->createPayment(
            (float) $payment->amount,
            $payment->currency,
            $reference,
            $customer
        );

        if (!($result['success'] ?? false)) {
            throw ValidationException::withMessages([
                'payment' => 'Unable to initialise the payment.',
            ]);
        }

        $payment->update([
            'transaction_reference' => $result['transaction_reference'] ?? null,
            'gateway_response' => $result,
        ]);

        return [
            'payment_id' => $payment->id,
            'payment_url' => route('sandbox.payment.show', $payment),
            'transaction_reference' => $result['transaction_reference'] ?? null,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
        ];
    }

    public function verifyPayment(Payment $payment): array
    {
        if (!$payment->transaction_reference) {
            throw ValidationException::withMessages([
                'payment' => 'The payment does not have a gateway transaction reference.',
            ]);
        }

        return $this->gateway->verifyPayment(
            $payment->transaction_reference
        );
    }

    public function retryPayment(
    Appointment $appointment,
    array $customer = []
): array {
    $appointment->loadMissing('doctorAvailability');

    if ($appointment->status !== 'pending_payment') {
        throw ValidationException::withMessages([
            'payment' => 'This appointment is not awaiting payment.',
        ]);
    }

    $latestPayment = $appointment->payments()
        ->latest('created_at')
        ->first();

    if (
        $latestPayment &&
        $latestPayment->status === 'pending' &&
        $latestPayment->expires_at &&
        $latestPayment->expires_at->isFuture()
    ) {
        return $this->initiatePayment($appointment, $customer);
    }

    if ($latestPayment && $latestPayment->status === 'pending') {
        $latestPayment->update([
            'status' => 'expired',
        ]);
    }

    $payment = $appointment->payments()->create([
        'patient_id' => $appointment->patient_id,
        'gateway' => config('services.payment.gateway', 'sandbox'),
        'amount' => $appointment->doctorAvailability->consultation_fee,
        'currency' => 'LKR',
        'status' => 'pending',
        'expires_at' => now()->addMinutes(10),
    ]);

    return $this->initiatePayment(
        $appointment->fresh(),
        $customer
    );
}
}