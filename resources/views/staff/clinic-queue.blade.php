<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800">
                Clinic Queue
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Manage today's patient queue and consultation progress.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            {{-- Success --}}
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error --}}
            @if (session('schedule'))
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                    {{ session('schedule') }}
                </div>
            @endif


            {{-- Clinic information --}}
            <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-xl">
                            🩺
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $availability->doctor->display_name }}
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Cardiology
                            </p>
                        </div>

                    </div>


                    <div class="text-left sm:text-right">

                        <p class="text-sm font-medium text-gray-500">
                            Clinic Time
                        </p>

                        <p class="mt-1 text-xl font-bold text-gray-900">
                            {{ \Carbon\Carbon::parse($availability->start_time)->format('h:i A') }}
                            –
                            {{ \Carbon\Carbon::parse($availability->end_time)->format('h:i A') }}
                        </p>

                    </div>

                </div>


                <div class="mt-5 grid gap-3 sm:grid-cols-3">

                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Booked
                        </p>

                        <p class="mt-1 text-2xl font-bold text-gray-900">
                            {{ $appointments->count() }}
                        </p>
                    </div>


                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Capacity
                        </p>

                        <p class="mt-1 text-2xl font-bold text-gray-900">
                            {{ $availability->capacity }}
                        </p>
                    </div>


                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Available
                        </p>

                        <p class="mt-1 text-2xl font-bold text-gray-900">
                            {{ max(0, $availability->capacity - $appointments->count()) }}
                        </p>
                    </div>

                </div>

            </div>


            {{-- Queue --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-100 px-6 py-5">

                    <h3 class="text-lg font-semibold text-gray-900">
                        Patient Queue
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Patients are displayed in permanent queue-number order.
                    </p>

                </div>


                @if ($appointments->isEmpty())

                    <div class="px-6 py-12 text-center">

                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-gray-50 text-xl">
                            👥
                        </div>

                        <p class="mt-3 text-sm font-semibold text-gray-900">
                            No patients in this clinic yet
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            Patients who complete payment will appear here.
                        </p>

                    </div>

                @else

                    <div class="divide-y divide-gray-100">

                        @foreach ($appointments as $appointment)

                            <div class="px-6 py-5 transition hover:bg-gray-50">

                                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                                    {{-- Patient --}}
                                    <div class="flex items-center gap-4">

                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-bold text-indigo-700">
                                            {{ $appointment->queue_number }}
                                        </div>

                                        <div>
                                            <h4 class="font-semibold text-gray-900">
                                                {{ $appointment->patient->name }}
                                            </h4>

                                            <p class="mt-1 text-sm text-gray-500">
                                                {{ $appointment->reason ?: 'No reason provided' }}
                                            </p>
                                        </div>

                                    </div>


                                    {{-- Status --}}
                                    <div>
                                        @switch($appointment->status)

                                            @case('confirmed')
                                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                                    Confirmed
                                                </span>
                                                @break

                                            @case('arrived')
                                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                                    Arrived
                                                </span>
                                                @break

                                            @case('in_progress')
                                                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                                                    In Progress
                                                </span>
                                                @break

                                            @case('completed')
                                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                    Completed
                                                </span>
                                                @break

                                        @endswitch
                                    </div>


                                    {{-- Action --}}
                                    <div class="lg:min-w-[180px] lg:text-right">

                                        @if ($appointment->status === 'confirmed')

                                            <form
                                                method="POST"
                                                action="{{ route('staff.appointment.status', [$appointment, 'arrived']) }}"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                                                >
                                                    Verify Arrival
                                                </button>
                                            </form>

                                        @elseif ($appointment->status === 'arrived')

                                            <form
                                                method="POST"
                                                action="{{ route('staff.appointment.status', [$appointment, 'in_progress']) }}"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                                                >
                                                    Start Consultation
                                                </button>
                                            </form>

                                        @elseif ($appointment->status === 'in_progress')

                                            <form
                                                method="POST"
                                                action="{{ route('staff.appointment.status', [$appointment, 'completed']) }}"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700"
                                                >
                                                    Complete Consultation
                                                </button>
                                            </form>

                                        @else

                                            <span class="text-sm font-medium text-gray-400">
                                                Consultation completed
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

        </div>
    </div>

</x-app-layout>