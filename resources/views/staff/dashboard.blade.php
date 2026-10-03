<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Staff Dashboard
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Cardiology clinic management
                </p>
            </div>

            <div class="text-sm text-gray-500">
                {{ $today->format('l, d F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mb-8">
                <h1 class="text-2xl font-semibold text-gray-900">
                    Today's Cardiology Clinics
                </h1>

                <p class="mt-2 text-sm text-gray-600">
                    View scheduled clinics and today's patient activity.
                </p>
            </div>

            @if ($clinics->isEmpty())

                <div class="rounded-2xl border border-gray-200 bg-white p-10 text-center shadow-sm">

                    <div class="text-4xl">
                        🩺
                    </div>

                    <h2 class="mt-4 text-lg font-semibold text-gray-900">
                        No Cardiology Clinics Today
                    </h2>

                    <p class="mt-2 text-sm text-gray-500">
                        There are no doctor clinics scheduled for today.
                    </p>

                </div>

            @else

                <div class="grid gap-6 lg:grid-cols-2">

                    @foreach ($clinics as $clinic)

                        <div
                            class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm
                                   transition-all duration-200 ease-out
                                   hover:-translate-y-1 hover:border-indigo-300 hover:shadow-lg"
                        >

                            <div class="flex items-start justify-between">

                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">
                                        {{ $clinic->doctor->display_name }}
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Cardiology
                                    </p>
                                </div>

                                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                    {{ $clinic->booked_count }} /
                                    {{ $clinic->capacity }} booked
                                </span>

                            </div>

                            <div class="mt-5 rounded-xl bg-gray-50 p-4">

                                <p class="text-sm font-medium text-gray-700">
                                    Clinic Time
                                </p>

                                <p class="mt-1 text-xl font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($clinic->start_time)->format('h:i A') }}
                                    –
                                    {{ \Carbon\Carbon::parse($clinic->end_time)->format('h:i A') }}
                                </p>

                            </div>

                            <div class="mt-5 grid grid-cols-3 gap-3">

                                <div class="rounded-xl bg-gray-50 p-4">
                                    <p class="text-xs text-gray-500">
                                        Arrived
                                    </p>

                                    <p class="mt-1 text-xl font-bold text-gray-900">
                                        {{ $clinic->arrived_count }}
                                    </p>
                                </div>

                                <div class="rounded-xl bg-gray-50 p-4">
                                    <p class="text-xs text-gray-500">
                                        In Progress
                                    </p>

                                    <p class="mt-1 text-xl font-bold text-indigo-600">
                                        {{ $clinic->in_progress_count }}
                                    </p>
                                </div>

                                <div class="rounded-xl bg-gray-50 p-4">
                                    <p class="text-xs text-gray-500">
                                        Completed
                                    </p>

                                    <p class="mt-1 text-xl font-bold text-green-600">
                                        {{ $clinic->completed_count }}
                                    </p>
                                </div>

                            </div>

                            <div class="mt-6">

                                <a
    href="{{ route('staff.clinic.queue', $clinic) }}"
    class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
>
    View Clinic Queue
</a>

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>
    </div>

</x-app-layout>