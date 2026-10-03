<?php

namespace App\Contracts;

interface PaymentGateway
{
    public function createPayment(
        float $amount,
        string $currency,
        string $reference,
        array $customer = []
    ): array;

    public function verifyPayment(
        string $transactionReference
    ): array;
}