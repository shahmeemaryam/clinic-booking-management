<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\StaffDashboardController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Patient\BookAppointment;
use App\Http\Controllers\SandboxPaymentController;
use App\Livewire\Patient\MyAppointments;
use App\Http\Controllers\PatientPaymentController;
use App\Http\Controllers\StaffDoctorController;
use App\Http\Controllers\StaffScheduleController;
use App\Http\Controllers\StaffClinicQueueController;


Route::get('/debug-auth', function (Request $request) {
    return response()->json([
        'authenticated' => Auth::check(),
        'user_id' => Auth::id(),
        'session_id' => $request->session()->getId(),
        'session_keys' => array_keys($request->session()->all()),
    ]);
});

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

   Route::get('/dashboard', function (Request $request) {
    $user = $request->user();

    if (!$user) {
        abort(401);
    }

    return match ($user->role) {
        'patient' => redirect()->route('patient.dashboard'),
        'doctor' => redirect()->route('doctor.dashboard'),
        'staff' => redirect()->route('staff.dashboard'),
        default => abort(403),
    };
})->name('dashboard');

    Route::get('/patient/dashboard', PatientDashboardController::class)
        ->middleware('role:patient')
        ->name('patient.dashboard');

    Route::get('/doctor/dashboard', DoctorDashboardController::class)
        ->middleware('role:doctor')
        ->name('doctor.dashboard');

    Route::get('/staff/dashboard', StaffDashboardController::class)
        ->middleware('role:staff')
        ->name('staff.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

        Route::get('/patient/book-appointment', BookAppointment::class)
    ->middleware('role:patient')
    ->name('patient.book-appointment');


    Route::get('/payment/sandbox/{payment}', [SandboxPaymentController::class, 'show'])
    ->middleware('role:patient')
    ->name('sandbox.payment.show');

Route::post('/payment/sandbox/{payment}/success', [SandboxPaymentController::class, 'success'])
    ->middleware('role:patient')
    ->name('sandbox.payment.success');

Route::post('/payment/sandbox/{payment}/failure', [SandboxPaymentController::class, 'failure'])
    ->middleware('role:patient')
    ->name('sandbox.payment.failure');

    Route::get('/patient/appointments', MyAppointments::class)
    ->middleware('role:patient')
    ->name('patient.appointments');

    Route::post('/patient/appointments/{appointment}/retry-payment', [
    PatientPaymentController::class,
    'retry',
])
    ->middleware('role:patient')
    ->name('patient.appointments.retry-payment');


    Route::middleware('role:staff')->group(function () {
    Route::get('/staff/doctors', [StaffDoctorController::class, 'index'])
        ->name('staff.doctors');

    Route::post('/staff/doctors', [StaffDoctorController::class, 'store'])
        ->name('staff.doctors.store');


        Route::get('/staff/schedules', [StaffScheduleController::class, 'index'])
    ->name('staff.schedules');

Route::post('/staff/schedules', [StaffScheduleController::class, 'store'])
    ->name('staff.schedules.store');

Route::put('/staff/schedules/{schedule}', [StaffScheduleController::class, 'update'])
    ->name('staff.schedules.update');

Route::delete('/staff/schedules/{schedule}', [StaffScheduleController::class, 'destroy'])
    ->name('staff.schedules.destroy');


    Route::get(
    '/staff/clinics/{availability}/queue',
    [StaffClinicQueueController::class, 'show']
)->name('staff.clinic.queue');

Route::post(
    '/staff/appointments/{appointment}/status/{status}',
    [StaffClinicQueueController::class, 'updateStatus']
)->name('staff.appointment.status');

Route::get(
    '/staff/clinic-queues',
    [StaffClinicQueueController::class, 'index']
)->name('staff.clinic.queues');
});
});


