<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoHospitalSeeder extends Seeder
{
    public function run(): void
    {
        $patient = User::updateOrCreate(
            ['email' => 'patient@test.com'],
            [
                'name' => 'Test Patient',
                'password' => 'Password@123',
            ]
        );

        $patient->role = 'patient';
        $patient->save();

        $patient2 = User::updateOrCreate(
            ['email' => 'patient2@test.com'],
            [
                'name' => 'Second Patient',
                'password' => 'Password@123',
            ]
        );

        $patient2->role = 'patient';
        $patient2->save();

        $staff = User::updateOrCreate(
            ['email' => 'staff@test.com'],
            [
                'name' => 'Test Staff',
                'password' => 'Password@123',
            ]
        );

        $staff->role = 'staff';
        $staff->save();

        $doctorUser = User::updateOrCreate(
            ['email' => 'doctor@test.com'],
            [
                'name' => 'Dr. Test Doctor',
                'password' => 'Password@123',
            ]
        );

        $doctorUser->role = 'doctor';
        $doctorUser->save();

        $specialty = Specialty::updateOrCreate(
            ['name' => 'Cardiology'],
            [
                'description' => 'Diagnosis and treatment of heart and cardiovascular conditions.',
            ]
        );

        $doctor = Doctor::updateOrCreate(
            ['user_id' => $doctorUser->id],
            [
                'specialty_id' => $specialty->id,
                'registration_number' => 'DOC-001',
                'bio' => 'Test doctor for the hospital appointment system.',
            ]
        );

        DoctorAvailability::updateOrCreate(
            [
                'doctor_id' => $doctor->id,
                'date' => '2026-10-05',
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
            ],
            [
                'capacity' => 20,
                'estimated_consultation_minutes' => 15,
                'current_queue_number' => null,
            ]
        );
    }
}
