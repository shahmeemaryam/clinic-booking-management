<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800">
                Doctor Management
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Manage doctors working at the Cardiology Clinic.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-8 lg:grid-cols-3">

                {{-- Add Doctor --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-1">

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Add Doctor
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Add a doctor to the Cardiology Clinic.
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('staff.doctors.store') }}"
                        class="space-y-5"
                    >
                        @csrf

                        <div>
                            <label
                                for="name"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Doctor Name
                            </label>

                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                placeholder="Dr. Perera"
                                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >

                            @error('name')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="registration_number"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Registration Number
                            </label>

                            <input
                                id="registration_number"
                                name="registration_number"
                                type="text"
                                value="{{ old('registration_number') }}"
                                placeholder="DOC-002"
                                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >

                            @error('registration_number')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="bio"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Short Bio
                            </label>

                            <textarea
                                id="bio"
                                name="bio"
                                rows="4"
                                placeholder="Optional doctor information"
                                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >{{ old('bio') }}</textarea>

                            @error('bio')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="rounded-lg bg-indigo-50 p-4 text-sm text-indigo-800">
                            This doctor will automatically be added to the Cardiology Clinic.
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white
                                   transition-all duration-200
                                   hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-md
                                   active:translate-y-0"
                        >
                            Add Doctor
                        </button>

                    </form>

                </div>

                {{-- Doctor List --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Doctors
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Doctors currently registered in the system.
                        </p>
                    </div>

                    <div class="space-y-4">

                        @forelse ($doctors as $doctor)

                            <div
                                class="flex items-center justify-between rounded-xl border border-gray-200 p-5
                                       transition-all duration-200
                                       hover:border-indigo-300 hover:bg-indigo-50/50 hover:shadow-sm"
                            >
                                <div>
                                    <h4 class="font-semibold text-gray-900">
                                        {{ $doctor->display_name }}
                                    </h4>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Registration:
                                        {{ $doctor->registration_number }}
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Cardiology
                                    </p>
                                </div>

                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                                    Active
                                </span>
                            </div>

                        @empty

                            <div class="rounded-xl bg-gray-50 p-6 text-center text-sm text-gray-500">
                                No doctors have been added yet.
                            </div>

                        @endforelse

                    </div>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>