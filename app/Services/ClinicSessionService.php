<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\DoctorAvailability;
use Carbon\CarbonInterface;


class ClinicSessionService
{
    public function generateForDoctorOnDate(
    Doctor $doctor,
    CarbonInterface $date
): array {
        $dateString = $date->toDateString();

        

        // A manually entered or API-based exception blocks the doctor's clinic.
        $hasException = $doctor->scheduleExceptions()
            ->whereDate('date', $dateString)
            ->exists();

        if ($hasException) {
            return [];
        }

        $dayOfWeek = $date->dayOfWeekIso;

        $schedules = $doctor->weeklySchedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('active', true)
            ->get();

        $sessions = [];

        foreach ($schedules as $schedule) {
            $sessions[] = DoctorAvailability::firstOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'date' => $dateString,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                ],
                [
    'capacity' => $schedule->capacity,
    'estimated_consultation_minutes' =>
        $schedule->estimated_consultation_minutes,
    'consultation_fee' => $schedule->consultation_fee,
    'current_queue_number' => null,
]
            );
        }

        return $sessions;
    }
}