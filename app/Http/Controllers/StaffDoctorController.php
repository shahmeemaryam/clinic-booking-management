<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffDoctorController extends Controller
{
    public function index(): View
    {
        $doctors = Doctor::query()
            ->with('specialty')
            ->orderBy('name')
            ->get();

        return view('staff.doctors', [
            'doctors' => $doctors,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'registration_number' => [
                'required',
                'string',
                'max:100',
                'unique:doctors,registration_number',
            ],

            'bio' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $cardiology = Specialty::where('name', 'Cardiology')->firstOrFail();

        Doctor::create([
            'name' => $validated['name'],
            'user_id' => null,
            'specialty_id' => $cardiology->id,
            'registration_number' => $validated['registration_number'],
            'bio' => $validated['bio'] ?? null,
        ]);

        return redirect()
            ->route('staff.doctors')
            ->with('success', 'Doctor added successfully.');
    }
}