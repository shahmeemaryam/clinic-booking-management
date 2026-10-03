<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use Illuminate\Support\Str;

class SandboxPaymentGateway implements PaymentGateway
{
    public function createPayment(
        float $amount,
        string $currency,
        string $reference,
        array $customer = []
    ): array {
        return [
            'success' => true,
            'payment_url' => null,
            'transaction_reference' => 'SANDBOX-' . Str::upper(Str::random(12)),
            'amount' => $amount,
            'currency' => $currency,
            'reference' => $reference,
        ];
    }

    public function verifyPayment(
        string $transactionReference
    ): array {
        return [
            'success' => true,
            'transaction_reference' => $transactionReference,
            'status' => 'paid',
            'test' => true,
        ];
    }
}