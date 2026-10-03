<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HolidayService
{
    public function holidaysForYear(int $year): array
    {
        return Cache::remember(
            "public-holidays:LK:{$year}",
            now()->addDay(),
            function () use ($year) {
                return Http::timeout(5)
                    ->retry(2, 200)
                    ->get("https://date.nager.at/api/v3/PublicHolidays/{$year}/LK")
                    ->throw()
                    ->json();
            }
        );
    }

    public function isPublicHoliday(string $date): ?array
    {
        $year = (int) substr($date, 0, 4);

        foreach ($this->holidaysForYear($year) as $holiday) {
            if (($holiday['date'] ?? null) === $date) {
                return $holiday;
            }
        }

        return null;
    }
}