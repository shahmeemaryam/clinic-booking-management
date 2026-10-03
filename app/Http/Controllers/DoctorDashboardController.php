<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DoctorDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('doctor.dashboard');
    }
}