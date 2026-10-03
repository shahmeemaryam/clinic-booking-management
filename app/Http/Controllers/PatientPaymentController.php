<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PatientPaymentController extends Controller
{
    public function retry(
        Appointment $appointment,
        PaymentService $paymentService
    ): RedirectResponse {
        abort_unless(
            $appointment->patient_id === Auth::id(),
            403
        );

        $patient = Auth::user();

        $result = $paymentService->retryPayment(
            $appointment,
            [
                'name' => $patient->name,
                'email' => $patient->email,
            ]
        );

        return redirect()->to($result['payment_url']);
    }
}