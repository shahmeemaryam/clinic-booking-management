<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Patient Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Welcome, {{ auth()->user()->name }}!
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Today's Weather
                    </h3>

                    @if ($weather)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-3xl font-bold text-gray-900">
                                    {{ $weather['temperature'] }}°C
                                </p>

                                <p class="text-gray-600 mt-1">
                                    {{ $weather['description'] }}
                                </p>
                            </div>

                            <div class="text-right">
                                <p class="text-sm text-gray-500">
                                    Feels like
                                </p>

                                <p class="text-lg font-semibold text-gray-800">
                                    {{ $weather['feels_like'] }}°C
                                </p>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 mt-4">
                            Weather provided by Open-Meteo
                        </p>
                    @else
                        <p class="text-gray-600">
                            Weather information is currently unavailable.
                        </p>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>