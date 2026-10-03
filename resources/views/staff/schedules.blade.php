<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800">
                Clinic Schedule Management
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Create and manage recurring cardiology clinic times.
            </p>
        </div>
    </x-slot>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Success message --}}
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-2.5 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Schedule error --}}
            @if ($errors->has('schedule'))
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-medium text-red-800">
                    {{ $errors->first('schedule') }}
                </div>
            @endif

            <div class="grid gap-8 lg:grid-cols-12">

                {{-- ========================================================= --}}
                {{-- CREATE CLINIC SCHEDULE --}}
                {{-- ========================================================= --}}
                <div class="lg:col-span-4 flex justify-center">

                    <div class="sticky top-6 w-full max-w-md rounded-2xl border border-indigo-100 bg-gradient-to-b from-indigo-50/80 via-white to-white p-6 shadow-sm">

                        {{-- Header --}}
                        <div class="mb-7">

                            <div class="flex items-start gap-3">

                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-lg shadow-sm">
    📅
</div>

                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">
                                        Create Clinic Schedule
                                    </h3>

                                    <p class="mt-1 text-sm leading-5 text-gray-500">
                                        Set the doctor's regular weekly clinic hours.
                                    </p>
                                </div>

                            </div>

                        </div>

                        <form
                            method="POST"
                            action="{{ route('staff.schedules.store') }}"
                            class="space-y-6"
                        >
                            @csrf

                            {{-- Doctor --}}
                            <div>
                                <label
                                    for="doctor_id"
                                    class="block text-sm font-semibold text-gray-700"
                                >
                                    Doctor
                                </label>

                                <p class="mt-1 text-xs text-gray-400">
                                    Select the doctor for this recurring clinic.
                                </p>

                                <select
                                    id="doctor_id"
                                    name="doctor_id"
                                    class="mt-3 block w-full rounded-xl border-gray-200 bg-white px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Select a doctor</option>

                                    @foreach ($doctors as $doctor)
                                        <option
                                            value="{{ $doctor->id }}"
                                            @selected(old('doctor_id') == $doctor->id)
                                        >
                                            {{ $doctor->display_name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('doctor_id')
                                    <p class="mt-2 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Clinic Day --}}
                            <div>
                                <label
                                    for="day_of_week"
                                    class="block text-sm font-semibold text-gray-700"
                                >
                                    Clinic Day
                                </label>

                                <p class="mt-1 text-xs text-gray-400">
                                    Choose the recurring day of the week.
                                </p>

                                <select
                                    id="day_of_week"
                                    name="day_of_week"
                                    class="mt-3 block w-full rounded-xl border-gray-200 bg-white px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Select a day</option>

                                    <option value="1" @selected(old('day_of_week') == 1)>
                                        Monday
                                    </option>

                                    <option value="2" @selected(old('day_of_week') == 2)>
                                        Tuesday
                                    </option>

                                    <option value="3" @selected(old('day_of_week') == 3)>
                                        Wednesday
                                    </option>

                                    <option value="4" @selected(old('day_of_week') == 4)>
                                        Thursday
                                    </option>

                                    <option value="5" @selected(old('day_of_week') == 5)>
                                        Friday
                                    </option>

                                    <option value="6" @selected(old('day_of_week') == 6)>
                                        Saturday
                                    </option>

                                    <option value="7" @selected(old('day_of_week') == 7)>
                                        Sunday
                                    </option>
                                </select>

                                @error('day_of_week')
                                    <p class="mt-2 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Divider --}}
                            <div class="border-t border-indigo-100"></div>


                            {{-- Clinic Hours --}}
                            <div>

                                <div class="mb-4">
                                    <h4 class="text-sm font-semibold text-gray-900">
                                        Clinic Hours
                                    </h4>

                                    <p class="mt-1 text-xs text-gray-400">
                                        Set the regular start and end time.
                                    </p>
                                </div>

                                <div class="space-y-4">

                                    {{-- Start --}}
                                    <div>
                                        <label
                                            for="start_time"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Starts
                                        </label>

                                        <input
                                            id="start_time"
                                            name="start_time"
                                            type="time"
                                            value="{{ old('start_time') }}"
                                            class="mt-2 block w-full rounded-xl border-gray-200 bg-white px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        >
                                    </div>

                                    {{-- End --}}
                                    <div>
                                        <label
                                            for="end_time"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Ends
                                        </label>

                                        <input
                                            id="end_time"
                                            name="end_time"
                                            type="time"
                                            value="{{ old('end_time') }}"
                                            class="mt-2 block w-full rounded-xl border-gray-200 bg-white px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        >
                                    </div>

                                </div>

                                @error('start_time')
                                    <p class="mt-2 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                                @error('end_time')
                                    <p class="mt-2 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>


                            {{-- Divider --}}
                            <div class="border-t border-gray-100"></div>


                            {{-- Clinic Settings --}}
                            <div>

                                <div class="mb-4">
                                    <h4 class="text-sm font-semibold text-gray-900">
                                        Clinic Settings
                                    </h4>

                                    <p class="mt-1 text-xs text-gray-400">
                                        Configure capacity, consultation duration and fee.
                                    </p>
                                </div>

                                <div class="space-y-5">

                                    {{-- Capacity --}}
                                    <div>
                                        <label
                                            for="capacity"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Patient Capacity
                                        </label>

                                        <input
                                            id="capacity"
                                            name="capacity"
                                            type="text"
                                            inputmode="numeric"
                                            value="{{ old('capacity', 20) }}"
                                            placeholder="20"
                                            class="mt-2 block w-full rounded-xl border-gray-200 bg-white px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        >

                                        <p class="mt-1.5 text-xs text-gray-400">
                                            Maximum patients for this clinic.
                                        </p>

                                        @error('capacity')
                                            <p class="mt-2 text-xs text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>


                                    {{-- Consultation Duration --}}
                                    <div>
                                        <label
                                            for="estimated_consultation_minutes"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Average Consultation Duration
                                        </label>

                                        <div class="mt-2 flex overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">

                                            <input
                                                id="estimated_consultation_minutes"
                                                name="estimated_consultation_minutes"
                                                type="text"
                                                inputmode="numeric"
                                                value="{{ old('estimated_consultation_minutes', 15) }}"
                                                placeholder="15"
                                                class="min-w-0 flex-1 border-0 bg-transparent px-4 py-2.5 text-sm focus:ring-0"
                                                required
                                            >

                                            <span class="flex items-center border-l border-gray-100 bg-gray-50 px-3 text-xs font-medium text-gray-500">
                                                min
                                            </span>

                                        </div>

                                        <p class="mt-1.5 text-xs text-gray-400">
                                            Used to estimate patient consultation times.
                                        </p>

                                        @error('estimated_consultation_minutes')
                                            <p class="mt-2 text-xs text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>


                                    {{-- Consultation Fee --}}
                                    <div>
                                        <label
                                            for="consultation_fee"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Consultation Fee
                                        </label>

                                        <div class="mt-2 flex overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">

                                            <span class="flex items-center border-r border-gray-100 bg-gray-50 px-3 text-xs font-semibold text-gray-500">
                                                LKR
                                            </span>

                                            <input
                                                id="consultation_fee"
                                                name="consultation_fee"
                                                type="text"
                                                inputmode="decimal"
                                                value="{{ old('consultation_fee', 2500) }}"
                                                placeholder="2500"
                                                class="min-w-0 flex-1 border-0 bg-transparent px-4 py-2.5 text-sm focus:ring-0"
                                                required
                                            >

                                        </div>

                                        <p class="mt-1.5 text-xs text-gray-400">
                                            Amount charged per consultation.
                                        </p>

                                        @error('consultation_fee')
                                            <p class="mt-2 text-xs text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                </div>

                            </div>


                            {{-- Create Button --}}
                            <button
    type="submit"
    class="group flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-md active:translate-y-0"
>
    <span>Create Clinic Schedule</span>

    <span class="transition-transform duration-200 group-hover:translate-x-1">
        →
    </span>
</button>

                        </form>

                    </div>
                </div>


                {{-- ========================================================= --}}
                {{-- EXISTING SCHEDULES --}}
                {{-- ========================================================= --}}
                <div class="lg:col-span-8">

                    {{-- Section Header --}}
                    <div class="mb-5">

                        <h3 class="text-lg font-semibold text-gray-900">
                            Doctor Clinic Schedules
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Recurring clinic times currently configured.
                        </p>

                    </div>


                    {{-- Schedule Cards --}}
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3">

                        @forelse ($schedules as $schedule)

                           <div
    x-data="{ editing: false }"
    class="group w-full rounded-2xl border border-gray-200 bg-white p-5 shadow-sm
           transition-all duration-200
           hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md
           {{ $loop->last && $loop->count % 3 === 1 ? 'md:col-start-2' : '' }}"
>

                                {{-- ================================================= --}}
                                {{-- UPDATE FORM --}}
                                {{-- ================================================= --}}
                                <form
                                    method="POST"
                                    action="{{ route('staff.schedules.update', $schedule) }}"
                                >
                                    @csrf
                                    @method('PUT')


                                    {{-- Card Header --}}
                                    <div class="flex items-start justify-between gap-3">

                                        <div class="flex min-w-0 items-center gap-3">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-base transition-colors duration-200 group-hover:bg-indigo-100">
                                                🩺
                                            </div>

                                            <div class="min-w-0">

                                                <h4 class="truncate text-base font-semibold text-gray-900">
                                                    {{ $schedule->doctor->display_name }}
                                                </h4>

                                                <p class="mt-0.5 text-sm font-medium text-indigo-600">
                                                    @switch($schedule->day_of_week)
                                                        @case(1)
                                                            Monday
                                                            @break

                                                        @case(2)
                                                            Tuesday
                                                            @break

                                                        @case(3)
                                                            Wednesday
                                                            @break

                                                        @case(4)
                                                            Thursday
                                                            @break

                                                        @case(5)
                                                            Friday
                                                            @break

                                                        @case(6)
                                                            Saturday
                                                            @break

                                                        @case(7)
                                                            Sunday
                                                            @break
                                                    @endswitch
                                                </p>

                                            </div>

                                        </div>


                                        {{-- Active badge --}}
                                        <span
                                            class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold
                                            {{ $schedule->active
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-gray-100 text-gray-600' }}"
                                        >
                                            {{ $schedule->active ? 'Active' : 'Inactive' }}
                                        </span>

                                    </div>


                                    {{-- ================================================= --}}
                                    {{-- VIEW MODE --}}
                                    {{-- ================================================= --}}
                                    <div
                                        x-cloak
                                        x-show="!editing"
                                        x-transition
                                        class="mt-5"
                                    >

                                        {{-- Clinic Hours --}}
                                        <div class="rounded-xl bg-gray-50 px-4 py-3.5">

                                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">
                                                Clinic Hours
                                            </p>

                                            <p class="mt-1 text-xl font-bold tracking-tight text-gray-900">
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}

                                                <span class="mx-1 text-gray-300">
                                                    –
                                                </span>

                                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                                            </p>

                                        </div>


                                        {{-- Summary --}}
                                        <div class="mt-3 grid grid-cols-3 gap-2">

                                            {{-- Capacity --}}
                                            <div class="rounded-xl border border-gray-100 bg-white px-3 py-3">
                                                <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                                    Capacity
                                                </p>

                                                <p class="mt-1 text-lg font-bold text-gray-900">
                                                    {{ $schedule->capacity }}
                                                </p>

                                                <p class="text-[11px] text-gray-400">
                                                    patients
                                                </p>
                                            </div>


                                            {{-- Consultation --}}
                                            <div class="rounded-xl border border-gray-100 bg-white px-3 py-3">
                                                <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                                    Consultation
                                                </p>

                                                <p class="mt-1 text-lg font-bold text-gray-900">
                                                    {{ $schedule->estimated_consultation_minutes }}

                                                    <span class="text-[11px] font-medium text-gray-400">
                                                        min
                                                    </span>
                                                </p>
                                            </div>


                                            {{-- Fee --}}
                                            <div class="rounded-xl border border-gray-100 bg-white px-3 py-3">
                                                <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                                    Fee
                                                </p>

                                                <p class="mt-1 text-sm font-bold text-gray-900">
                                                    LKR {{ number_format((float) $schedule->consultation_fee, 0) }}
                                                </p>
                                            </div>

                                        </div>

                                    </div>


                                    {{-- ================================================= --}}
                                    {{-- EDIT MODE --}}
                                    {{-- ================================================= --}}
                                    <div
                                        x-cloak
                                        x-show="editing"
                                        x-transition
                                        class="mt-5"
                                    >

                                        <div class="rounded-xl border border-indigo-100 bg-indigo-50/40 p-4">

                                            <div class="mb-4">
                                                <h5 class="text-sm font-semibold text-gray-900">
                                                    Edit Clinic Schedule
                                                </h5>

                                                <p class="mt-1 text-xs text-gray-500">
                                                    Update the recurring clinic details.
                                                </p>
                                            </div>


                                            <div class="space-y-4">

                                                {{-- Day --}}
                                                <div>
                                                    <label
                                                        for="edit_day_{{ $schedule->id }}"
                                                        class="block text-sm font-medium text-gray-700"
                                                    >
                                                        Clinic Day
                                                    </label>

                                                    <select
                                                        id="edit_day_{{ $schedule->id }}"
                                                        name="day_of_week"
                                                        class="mt-2 block w-full rounded-xl border-gray-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                        required
                                                    >
                                                        <option value="1" @selected($schedule->day_of_week == 1)>
                                                            Monday
                                                        </option>

                                                        <option value="2" @selected($schedule->day_of_week == 2)>
                                                            Tuesday
                                                        </option>

                                                        <option value="3" @selected($schedule->day_of_week == 3)>
                                                            Wednesday
                                                        </option>

                                                        <option value="4" @selected($schedule->day_of_week == 4)>
                                                            Thursday
                                                        </option>

                                                        <option value="5" @selected($schedule->day_of_week == 5)>
                                                            Friday
                                                        </option>

                                                        <option value="6" @selected($schedule->day_of_week == 6)>
                                                            Saturday
                                                        </option>

                                                        <option value="7" @selected($schedule->day_of_week == 7)>
                                                            Sunday
                                                        </option>
                                                    </select>
                                                </div>


                                                {{-- Start --}}
                                                <div>
                                                    <label
                                                        for="edit_start_{{ $schedule->id }}"
                                                        class="block text-sm font-medium text-gray-700"
                                                    >
                                                        Start Time
                                                    </label>

                                                    <input
                                                        id="edit_start_{{ $schedule->id }}"
                                                        type="time"
                                                        name="start_time"
                                                        value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}"
                                                        class="mt-2 block w-full rounded-xl border-gray-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                        required
                                                    >
                                                </div>


                                                {{-- End --}}
                                                <div>
                                                    <label
                                                        for="edit_end_{{ $schedule->id }}"
                                                        class="block text-sm font-medium text-gray-700"
                                                    >
                                                        End Time
                                                    </label>

                                                    <input
                                                        id="edit_end_{{ $schedule->id }}"
                                                        type="time"
                                                        name="end_time"
                                                        value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}"
                                                        class="mt-2 block w-full rounded-xl border-gray-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                        required
                                                    >
                                                </div>


                                                {{-- Capacity --}}
                                                <div>
                                                    <label
                                                        for="edit_capacity_{{ $schedule->id }}"
                                                        class="block text-sm font-medium text-gray-700"
                                                    >
                                                        Patient Capacity
                                                    </label>

                                                    <input
                                                        id="edit_capacity_{{ $schedule->id }}"
                                                        type="text"
                                                        inputmode="numeric"
                                                        name="capacity"
                                                        value="{{ $schedule->capacity }}"
                                                        class="mt-2 block w-full rounded-xl border-gray-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                        required
                                                    >
                                                </div>


                                                {{-- Consultation --}}
                                                <div>
                                                    <label
                                                        for="edit_minutes_{{ $schedule->id }}"
                                                        class="block text-sm font-medium text-gray-700"
                                                    >
                                                        Average Consultation Duration
                                                    </label>

                                                    <div class="mt-2 flex overflow-hidden rounded-xl border border-gray-200 bg-white focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">

                                                        <input
                                                            id="edit_minutes_{{ $schedule->id }}"
                                                            type="text"
                                                            inputmode="numeric"
                                                            name="estimated_consultation_minutes"
                                                            value="{{ $schedule->estimated_consultation_minutes }}"
                                                            class="min-w-0 flex-1 border-0 bg-transparent px-4 py-2.5 text-sm focus:ring-0"
                                                            required
                                                        >

                                                        <span class="flex items-center border-l border-gray-100 bg-gray-50 px-3 text-xs text-gray-500">
                                                            min
                                                        </span>

                                                    </div>
                                                </div>


                                                {{-- Fee --}}
                                                <div>
                                                    <label
                                                        for="edit_fee_{{ $schedule->id }}"
                                                        class="block text-sm font-medium text-gray-700"
                                                    >
                                                        Consultation Fee
                                                    </label>

                                                    <div class="mt-2 flex overflow-hidden rounded-xl border border-gray-200 bg-white focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">

                                                        <span class="flex items-center border-r border-gray-100 bg-gray-50 px-3 text-xs font-semibold text-gray-500">
                                                            LKR
                                                        </span>

                                                        <input
                                                            id="edit_fee_{{ $schedule->id }}"
                                                            type="text"
                                                            inputmode="decimal"
                                                            name="consultation_fee"
                                                            value="{{ $schedule->consultation_fee }}"
                                                            class="min-w-0 flex-1 border-0 bg-transparent px-4 py-2.5 text-sm focus:ring-0"
                                                            required
                                                        >

                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                    </div>


                                    {{-- ================================================= --}}
                                    {{-- ACTIONS --}}
                                    {{-- ================================================= --}}
                                    <div class="mt-4 flex flex-wrap items-center gap-2">

                                        {{-- Edit --}}
                                        <button
                                            type="button"
                                            x-cloak
                                            x-show="!editing"
                                            @click="editing = true"
                                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-semibold text-gray-700 transition-all duration-200 hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 hover:shadow-sm"
                                        >
                                            <span>✏️</span>
                                            Edit Schedule
                                        </button>


                                        {{-- Save --}}
                                        <button
                                            type="submit"
                                            x-cloak
                                            x-show="editing"
                                            class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition-all duration-200 hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-md"
                                        >
                                            Save Changes
                                        </button>


                                        {{-- Cancel --}}
                                        <button
                                            type="button"
                                            x-cloak
                                            x-show="editing"
                                            @click="editing = false"
                                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100"
                                        >
                                            Cancel
                                        </button>

                                    </div>

                                </form>


                                {{-- DELETE SCHEDULE --}}
                                

                                    <div class="mt-2">

                                        <form
                                            method="POST"
                                            action="{{ route('staff.schedules.destroy', $schedule) }}"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
    type="submit"
    onclick="return confirm('Delete this clinic schedule? This action cannot be undone.')"
    class="inline-flex items-center rounded-lg px-3.5 py-2 text-sm font-semibold text-red-600 transition-all duration-200 hover:bg-red-50 hover:text-red-700"
>
    Delete Schedule
</button>

                                        </form>

                                    </div>

                                

                            </div>

                        @empty

                            <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-sm md:col-span-2">

                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-gray-50 text-xl">
                                    📅
                                </div>

                                <p class="mt-3 text-sm font-semibold text-gray-900">
                                    No clinic schedules yet
                                </p>

                                <p class="mt-1 text-sm text-gray-500">
                                    Create a recurring clinic schedule using the form.
                                </p>

                            </div>

                        @endforelse

                    </div>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>