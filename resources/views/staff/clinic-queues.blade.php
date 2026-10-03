<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800">
                Clinic Queue
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Select a clinic to view and manage its patient queue.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if ($clinics->isEmpty())

                <div class="rounded-2xl border border-gray-200 bg-white p-10 text-center shadow-sm">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl">
                        🩺
                    </div>

                    <h3 class="mt-4 text-lg font-semibold text-gray-900">
                        No Upcoming Clinics
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        There are no clinic sessions scheduled from today onward.
                    </p>

                </div>

            @else

                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">

                    @foreach ($clinics as $clinic)

                        @php
                            $bookedCount = $clinic->appointments
                                ->whereIn('status', [
                                    'confirmed',
                                    'arrived',
                                    'in_progress',
                                    'completed',
                                ])
                                ->count();
                        @endphp

                        <div class="group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">

                            <div class="flex items-start justify-between gap-3">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-lg">
                                        🩺
                                    </div>

                                    <div>
                                        <h3 class="font-semibold text-gray-900">
                                            {{ $clinic->doctor->display_name }}
                                        </h3>

                                        <p class="text-sm text-indigo-600">
                                            Cardiology
                                        </p>
                                    </div>

                                </div>

                                @if ($clinic->date->isToday())

                                    <span class="rounded-full bg-green-100 px-2.5 py-1 text-[11px] font-semibold text-green-700">
                                        Today
                                    </span>

                                @elseif ($clinic->date->isTomorrow())

                                    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-[11px] font-semibold text-blue-700">
                                        Tomorrow
                                    </span>

                                @endif

                            </div>


                            <div class="mt-5 rounded-xl bg-gray-50 px-4 py-3">

                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Clinic Date
                                </p>

                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $clinic->date->format('l, d F Y') }}
                                </p>

                                <p class="mt-1 text-lg font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($clinic->start_time)->format('h:i A') }}
                                    –
                                    {{ \Carbon\Carbon::parse($clinic->end_time)->format('h:i A') }}
                                </p>

                            </div>


                            <div class="mt-3 grid grid-cols-2 gap-2">

                                <div class="rounded-xl border border-gray-100 px-3 py-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                        Booked
                                    </p>

                                    <p class="mt-1 text-lg font-bold text-gray-900">
                                        {{ $bookedCount }}
                                    </p>
                                </div>

                                <div class="rounded-xl border border-gray-100 px-3 py-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                        Capacity
                                    </p>

                                    <p class="mt-1 text-lg font-bold text-gray-900">
                                        {{ $clinic->capacity }}
                                    </p>
                                </div>

                            </div>


                            <div class="mt-4">

                                <a
                                    href="{{ route('staff.clinic.queue', $clinic) }}"
                                    class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
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