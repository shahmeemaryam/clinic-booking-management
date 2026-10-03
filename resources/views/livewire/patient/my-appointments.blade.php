<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
@if (session('success'))
    <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
        {{ session('error') }}
    </div>
@endif
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            My Appointments
        </h1>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            View your clinic bookings, payment status and queue information.
        </p>
    </div>

    @if ($appointments->isEmpty())

        <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="text-4xl">📅</div>

            <h2 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">
                No appointments yet
            </h2>

            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Book an appointment to see your clinic details here.
            </p>

            <a
                href="{{ route('patient.book-appointment') }}"
                class="mt-6 inline-flex rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white transition hover:bg-indigo-700 hover:shadow-md"
            >
                Book an Appointment
            </a>
        </div>

    @else

        <div class="space-y-6">

            @foreach ($appointments as $appointment)

                @php
                    $session = $appointment->doctorAvailability;
                    $doctor = $session?->doctor;
                    $latestPayment = $appointment->latestPayment;
                @endphp

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                        <div>
                            <div class="flex items-center gap-3">

                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
    {{ $doctor?->display_name ?? 'Doctor' }}
</h2>

                                @if ($appointment->status === 'confirmed')
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                                        Confirmed
                                    </span>
                                @elseif ($appointment->status === 'pending_payment')
                                    <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800">
                                        Payment Pending
                                    </span>
                                @elseif ($appointment->status === 'in_progress')
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">
                                        In Progress
                                    </span>
                                @elseif ($appointment->status === 'completed')
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                        Completed
                                    </span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                        {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                    </span>
                                @endif

                            </div>

                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $doctor?->specialty?->name }}
                            </p>
                        </div>

                     @if ($appointment->status === 'pending_payment' && $latestPayment)

    @if (
        $latestPayment->status === 'pending'
        && $latestPayment->expires_at
        && $latestPayment->expires_at->isFuture()
    )

        <a
            href="{{ route('sandbox.payment.show', $latestPayment) }}"
            class="inline-flex rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white transition-all duration-200 hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-md"
        >
            Complete Payment
        </a>

    @elseif (
        $latestPayment->status === 'failed'
        || (
            $latestPayment->status === 'pending'
            && $latestPayment->expires_at
            && $latestPayment->expires_at->isPast()
        )
    )

        <form
            method="POST"
            action="{{ route('patient.appointments.retry-payment', $appointment) }}"
        >
            @csrf

            <button
                type="submit"
                class="inline-flex rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white transition-all duration-200 hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-md"
            >
                Retry Payment
            </button>
        </form>

    @endif


    @if (
    $appointment->status === 'confirmed'
    && $session
    && $session->date
    && $session->date->isFuture()
)
    <button
        type="button"
        wire:click="cancelAppointment({{ $appointment->id }})"
        wire:confirm="Are you sure you want to cancel this appointment?"
        class="inline-flex items-center rounded-lg border border-red-200 bg-white px-5 py-3 font-semibold text-red-600 transition-all duration-200 hover:-translate-y-0.5 hover:bg-red-50 hover:text-red-700 hover:shadow-sm"
    >
        Cancel Appointment
    </button>
@endif
@endif

                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                        <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-700/50">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Date
                            </p>

                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                {{ $session?->date?->format('d M Y') }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-700/50">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Clinic
                            </p>

                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($session?->start_time)->format('h:i A') }}
                                –
                                {{ \Carbon\Carbon::parse($session?->end_time)->format('h:i A') }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-700/50">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Queue Number
                            </p>

                            <p class="mt-1 text-xl font-bold text-indigo-600">
                                {{ $appointment->queue_number ?? '—' }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-700/50">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Consultation Fee
                            </p>

                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                LKR {{ number_format((float) $session?->consultation_fee, 2) }}
                            </p>
                        </div>

                    </div>

                    @if ($appointment->estimatedTime)

                        <div class="mt-5 rounded-xl border border-indigo-200 bg-indigo-50 p-5 dark:border-indigo-900 dark:bg-indigo-950/30">

                            <p class="text-sm font-medium text-indigo-900 dark:text-indigo-200">
                                Estimated Consultation Time
                            </p>

                            <p class="mt-1 text-xl font-bold text-indigo-700 dark:text-indigo-300">
                                {{ $appointment->estimatedTime->format('h:i A') }}
                            </p>

                            <p class="mt-2 text-xs text-indigo-700 dark:text-indigo-300">
                                This is an estimate and may change depending on consultation duration and queue progress.
                            </p>

                        </div>

                    @endif

                    <div class="mt-5 flex flex-wrap gap-3 text-sm">

                        <span class="text-gray-500 dark:text-gray-400">
                            Payment:
                        </span>

                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ ucfirst($latestPayment?->status ?? 'Not started') }}
                        </span>

                        @if ($latestPayment?->transaction_reference)
                            <span class="text-gray-400">
                                •
                            </span>

                            <span class="text-gray-500 dark:text-gray-400">
                                {{ $latestPayment->transaction_reference }}
                            </span>
                        @endif

                    </div>
                    @if (
    $appointment->status === 'confirmed'
    && $session
    && $session->date
    && $session->date->isFuture()
)
    <div class="mt-5 flex justify-end border-t border-gray-100 pt-5">

        <button
            type="button"
            wire:click="cancelAppointment({{ $appointment->id }})"
            wire:confirm="Are you sure you want to cancel this appointment?"
            class="inline-flex items-center rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-600 transition-all duration-200 hover:-translate-y-0.5 hover:bg-red-50 hover:text-red-700 hover:shadow-sm"
        >
            Cancel Appointment
        </button>

    </div>
@endif

                </div>

            @endforeach

        </div>

    @endif

</div>