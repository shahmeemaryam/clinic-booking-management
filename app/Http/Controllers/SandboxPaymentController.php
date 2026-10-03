<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\AppointmentBookingService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SandboxPaymentController extends Controller
{
    private function authorizePayment(Payment $payment): void
    {
        abort_unless(
            $payment->patient_id === Auth::id(),
            403
        );
    }

    public function show(Payment $payment): View
    {
        $this->authorizePayment($payment);

        $payment->load('appointment.doctorAvailability', 'patient');

        abort_unless($payment->status === 'pending', 404);

        return view('payments.sandbox', [
            'payment' => $payment,
        ]);
    }

    public function success(
        Payment $payment,
        PaymentService $paymentService,
        AppointmentBookingService $bookingService
    ): RedirectResponse {
        $this->authorizePayment($payment);

        abort_unless($payment->status === 'pending', 404);

        $result = $paymentService->verifyPayment($payment);

        if (
            !($result['success'] ?? false) ||
            ($result['status'] ?? null) !== 'paid'
        ) {
            return redirect()
                ->route('sandbox.payment.show', $payment)
                ->with('error', 'Payment verification failed.');
        }

        $appointment = $bookingService->confirmPayment(
            $payment->id,
            $result['transaction_reference'],
            $result
        );

        return redirect()
    ->route('patient.appointments')
    ->with(
        'success',
        "Payment successful. Your queue number is {$appointment->queue_number}."
    );
    }

    public function failure(Payment $payment): RedirectResponse
    {
        $this->authorizePayment($payment);

        abort_unless($payment->status === 'pending', 404);

        $payment->update([
            'status' => 'failed',
        ]);

        return redirect()
    ->route('patient.appointments')
    ->with('error', 'Payment was not completed. You can retry the payment from your appointment.');
    }
}