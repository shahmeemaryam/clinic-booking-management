<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WeatherService
{
    public function today(): array
    {
        return Cache::remember(
            'clinic-weather-today',
            now()->addMinutes(30),
            function () {
                $latitude = config('services.weather.latitude');
                $longitude = config('services.weather.longitude');

                if ($latitude === null || $longitude === null) {
                    throw new RuntimeException(
                        'Weather coordinates are not configured.'
                    );
                }

                $response = Http::timeout(10)
                    ->retry(2, 200)
                    ->acceptJson()
                    ->get('https://api.open-meteo.com/v1/forecast', [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'current' => 'temperature_2m,apparent_temperature,weather_code',
                        'timezone' => 'Asia/Colombo',
                    ])
                    ->throw()
                    ->json();

                $current = $response['current'] ?? [];

                return [
                    'temperature' => $current['temperature_2m'] ?? null,
                    'feels_like' => $current['apparent_temperature'] ?? null,
                    'weather_code' => $current['weather_code'] ?? null,
                    'description' => $this->description(
                        $current['weather_code'] ?? null
                    ),
                ];
            }
        );
    }

    private function description(?int $code): string
    {
        return match ($code) {
            0 => 'Clear sky',
            1 => 'Mainly clear',
            2 => 'Partly cloudy',
            3 => 'Overcast',
            45, 48 => 'Foggy',
            51, 53, 55 => 'Drizzle',
            56, 57 => 'Freezing drizzle',
            61, 63, 65 => 'Rain',
            66, 67 => 'Freezing rain',
            71, 73, 75, 77 => 'Snow',
            80, 81, 82 => 'Rain showers',
            85, 86 => 'Snow showers',
            95 => 'Thunderstorm',
            96, 99 => 'Thunderstorm with hail',
            default => 'Weather unavailable',
        };
    }
}