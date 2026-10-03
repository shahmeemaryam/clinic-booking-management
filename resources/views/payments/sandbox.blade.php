<x-app-layout>
    <div class="mx-auto max-w-2xl px-4 py-10">

        <div class="rounded-xl bg-white p-8 shadow">

            <h1 class="text-2xl font-bold text-gray-900">
                Sandbox Payment
            </h1>

            <p class="mt-2 text-sm text-gray-600">
                Development payment environment. No real money will be charged.
            </p>

            <div class="mt-6 rounded-lg bg-gray-50 p-5">
                <div class="flex justify-between py-2">
                    <span>Patient</span>
                    <strong>{{ $payment->patient->name }}</strong>
                </div>

                <div class="flex justify-between py-2">
                    <span>Amount</span>
                    <strong>
                        {{ $payment->currency }}
                        {{ number_format((float) $payment->amount, 2) }}
                    </strong>
                </div>

                <div class="flex justify-between py-2">
                    <span>Appointment</span>
                    <strong>#{{ $payment->appointment_id }}</strong>
                </div>

                <div class="flex justify-between py-2">
                    <span>Payment Status</span>
                    <strong>{{ ucfirst($payment->status) }}</strong>
                </div>
            </div>

           @if (session('error'))
    <div style="margin-top: 20px; background-color: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; border: 1px solid #fca5a5;">
        {{ session('error') }}
    </div>
@endif

           <div style="margin-top: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">

    <form
        method="POST"
        action="{{ route('sandbox.payment.success', $payment) }}"
    >
        @csrf

        <button
            type="submit"
            style="background-color: #16a34a; color: #ffffff; padding: 12px 20px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer;"
        >
            Simulate Successful Payment
        </button>
    </form>

    <form
        method="POST"
        action="{{ route('sandbox.payment.failure', $payment) }}"
    >
        @csrf

        <button
            type="submit"
            style="background-color: #dc2626; color: #ffffff; padding: 12px 20px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer;"
        >
            Simulate Failed Payment
        </button>
    </form>

</div>
        </div>
    </div>
</x-app-layout>