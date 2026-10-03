<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PatientDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('patient.dashboard');
    }
}