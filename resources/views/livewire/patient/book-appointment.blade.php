<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">

    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            Book an Appointment
        </h1>


            <p class="mt-1 text-gray-600 dark:text-gray-400">
                How would you like to find an appointment?
            </p>
        </div>

        <div class="grid gap-6 md:grid-cols-2">

            <button
                type="button"
                wire:click="chooseMode('doctor')"
                class="group rounded-2xl border border-gray-200 bg-white p-7 text-left shadow-sm
       transition-all duration-200 ease-out
       hover:-translate-y-1 hover:border-indigo-300 hover:shadow-xl
       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
       dark:border-gray-700 dark:bg-gray-800
       dark:hover:border-indigo-600 dark:hover:shadow-indigo-950/30"
            >
                <div class="text-3xl transition-transform duration-200 group-hover:scale-110">
    👨‍⚕️
</div>

                <h3 class="mt-4 text-lg font-semibold text-gray-900 transition-colors group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400">
                    I Have a Doctor in Mind
                </h3>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Find a specific doctor and view their regular clinic schedule.
                </p>
            </button>

            <button
                type="button"
                wire:click="chooseMode('date')"
                class="group rounded-2xl border border-gray-200 bg-white p-7 text-left shadow-sm
       transition-all duration-200 ease-out
       hover:-translate-y-1 hover:border-indigo-300 hover:shadow-xl
       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
       dark:border-gray-700 dark:bg-gray-800
       dark:hover:border-indigo-600 dark:hover:shadow-indigo-950/30"
            >
                <div class="text-3xl transition-transform duration-200 group-hover:scale-110">
    📅
</div>

                <h3 class="mt-4 text-lg font-semibold text-gray-900 transition-colors group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400">
                    Show Me Available Clinics
                </h3>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Find a clinic on a date that works for you.
                </p>
            </button>

        </div>

    

    {{-- FLOW 1 --}}
    @if ($mode === 'doctor' && !$doctorId)

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Doctors
                </h2>

                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Choose the doctor you would like to see.
                </p>
            </div>

            <button
                type="button"
                wire:click="backToStart"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
            >
                Change Search
            </button>
        </div>

        <div class="grid gap-5 md:grid-cols-2">

            @forelse ($doctors as $doctor)

                <div class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm
       transition-all duration-200 ease-out
       hover:-translate-y-1 hover:border-indigo-300 hover:shadow-lg
       dark:border-gray-700 dark:bg-gray-800
       dark:hover:border-indigo-700">

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $doctor->display_name }}
                    </h3>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Registration No:
                        {{ $doctor->registration_number }}
                    </p>

                    <button
                        type="button"
                        wire:click="chooseDoctor({{ $doctor->id }})"
                        class="mt-5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white
       transition-all duration-200
       hover:bg-indigo-700 hover:shadow-md
       active:scale-95"
                    >
                        View Schedule
                    </button>

                </div>

            @empty

                <div class="rounded-xl bg-gray-50 p-6 text-sm text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                    No doctors are currently available.
                </div>

            @endforelse

        </div>

    @endif

    {{-- FLOW 1: SELECTED DOCTOR --}}
    @if ($mode === 'doctor' && $doctorId)

        <div class="mb-6 flex items-center justify-between">

            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ \App\Models\Doctor::find($doctorId)?->display_name }}
                </h2>

                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Regular Clinic Schedule
                </p>
            </div>

            <button
                type="button"
                wire:click="backToStart"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
            >
                Back
            </button>

        </div>

        @if (count($weeklySchedules))
            <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">

                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white">
                        Regular Clinic Times
                    </h3>
                </div>

                <div class="divide-y divide-gray-200 dark:divide-gray-700">

                    @foreach ($weeklySchedules as $schedule)
                        <div class="flex items-center justify-between px-6 py-4">
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $schedule['day'] }}
                            </span>

                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $schedule['start'] }}
                                –
                                {{ $schedule['end'] }}
                            </span>
                        </div>
                    @endforeach

                </div>
            </div>
        @else
            <div class="mb-8 rounded-xl bg-gray-50 p-5 text-sm text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                This doctor does not currently have a regular clinic schedule.
            </div>
        @endif

        <div class="mb-6">
            <label
                for="doctorDate"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
            >
                Choose a Date
            </label>

            <input
                id="doctorDate"
                type="date"
                wire:model.live="selectedDate"
                min="{{ $minBookingDate }}"
                max="{{ $maxBookingDate }}"
                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
            >

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Appointments can be booked up to 7 days in advance.
            </p>
        </div>

        @if ($message)
            <div class="mb-6 rounded-xl bg-gray-50 p-5 text-sm text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                {{ $message }}
            </div>
        @endif

        @if (count($availabilities))

            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                Available Clinic Sessions
            </h3>

            <div class="grid gap-4 md:grid-cols-2">

                @foreach ($availabilities as $session)

                    <button
                        type="button"
                        wire:click="selectAvailability({{ $session['id'] }})"
                        class="rounded-2xl border bg-white p-5 text-left shadow-sm
       transition-all duration-200 ease-out
       hover:-translate-y-1 hover:border-indigo-400 hover:bg-indigo-50 hover:shadow-lg
       focus:outline-none focus:ring-2 focus:ring-indigo-500
       dark:bg-gray-800 dark:hover:border-indigo-600 dark:hover:bg-indigo-950/30
       {{ $availabilityId == $session['id']
            ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200 dark:border-indigo-500 dark:bg-indigo-950/40 dark:ring-indigo-900'
            : 'border-gray-200 dark:border-gray-700' }}"
                    >

                        <div class="flex items-start justify-between">

                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    {{ $session['start'] }} – {{ $session['end'] }}
                                </p>

                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $session['remaining'] }} places available
                                </p>
                            </div>

                            <span class="text-sm font-semibold text-indigo-600">
                                LKR {{ $session['fee'] }}
                            </span>

                        </div>

                        <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                            Estimated consultation:
                            {{ $session['estimated_minutes'] }} minutes
                        </p>

                    </button>

                @endforeach

            </div>

        @endif

    @endif

    {{-- FLOW 2 --}}
    @if ($mode === 'date')

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Find an Available Clinic
                </h2>

                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Choose a date and see which doctors are available.
                </p>
            </div>

            <button
                type="button"
                wire:click="backToStart"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
            >
                Back
            </button>
        </div>

        <div class="mb-8">

            <label
                for="availableDate"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
            >
                Choose a Date
            </label>

            <input
                id="availableDate"
                type="date"
                wire:model.live="selectedDate"
                min="{{ $minBookingDate }}"
                max="{{ $maxBookingDate }}"
                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
            >

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                You can choose a date from today up to 7 days ahead.
            </p>

        </div>

        @if ($message)
            <div class="mb-6 rounded-xl bg-gray-50 p-5 text-sm text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                {{ $message }}
            </div>
        @endif

        @if (count($availableDoctors))

            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                Available Clinics
            </h3>

            <div class="space-y-5">

                @foreach ($availableDoctors as $doctor)

                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

                        <div class="mb-5">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Dr. {{ $doctor['name'] }}
                            </h4>

                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Registration No:
                                {{ $doctor['registration_number'] }}
                            </p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">

                            @foreach ($doctor['sessions'] as $session)

                                <button
                                    type="button"
                                    wire:click="selectAvailability({{ $session['id'] }})"
                                    class="group rounded-xl border border-gray-200 bg-white p-5 text-left
       shadow-sm
       transition-all duration-200 ease-out
       hover:-translate-y-1
       hover:border-indigo-400
       hover:bg-indigo-50
       hover:shadow-lg
       focus:outline-none
       focus:ring-2
       focus:ring-indigo-500
       dark:border-gray-700
       dark:bg-gray-800
       dark:hover:border-indigo-600
       dark:hover:bg-indigo-950/30"
                                >

                                    <div class="flex justify-between">

                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ $session['start'] }}
                                            –
                                            {{ $session['end'] }}
                                        </span>

                                        <span class="text-sm font-semibold text-indigo-600">
                                            LKR {{ $session['fee'] }}
                                        </span>

                                    </div>

                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $session['remaining'] }}
                                        places available
                                    </p>

                                </button>

                            @endforeach

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    @endif

    {{-- SELECTED SESSION / PAYMENT --}}
    @if ($availabilityId)

        <div class="mt-8 rounded-2xl border border-indigo-200 bg-indigo-50 p-6 dark:border-indigo-900 dark:bg-indigo-950/30">

            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Appointment Details
            </h3>

            <div class="mt-4">
                <label
                    for="reason"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Reason for Visit
                </label>

                <textarea
                    id="reason"
                    wire:model="reason"
                    rows="3"
                    maxlength="500"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    placeholder="Briefly describe the reason for your visit..."
                ></textarea>

                @error('reason')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @error('availabilityId')
                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @error('booking')
                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <button
                type="button"
                wire:click="proceedToPayment"
                wire:loading.attr="disabled"
                class="mt-5 rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-500 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="proceedToPayment">
                    Proceed to Payment
                </span>

                <span wire:loading wire:target="proceedToPayment">
                    Preparing Payment...
                </span>
            </button>

        </div>

    @endif

</div>