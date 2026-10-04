<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\View\View;
use Throwable;

class PatientDashboardController extends Controller
{
    public function __invoke(WeatherService $weatherService): View
    {
        $weather = null;

        try {
            $weather = $weatherService->today();
        } catch (Throwable $exception) {
            report($exception);
        }

        return view('patient.dashboard', [
            'weather' => $weather,
        ]);
    }
}