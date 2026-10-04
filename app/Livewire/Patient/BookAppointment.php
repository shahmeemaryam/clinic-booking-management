<?php

namespace App\Livewire\Patient;
use Illuminate\Support\Facades\Log;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\Specialty;
use App\Services\AppointmentBookingService;
use App\Services\ClinicSessionService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BookAppointment extends Component
{
    public $doctors = [];

    public $availableDoctors = [];

    public $weeklySchedules = [];

    public $availabilities = [];

    public $doctorId = null;

    public $selectedDate = null;

    public $availabilityId = null;

    public $mode = null;

    public $reason = '';

    public $minBookingDate;

    public $maxBookingDate;

    public $message = '';

    public function mount(): void
    {
        if (! Auth::check() || Auth::user()?->role !== 'patient') {
    $this->redirectRoute('login');
    return;
}

        $this->minBookingDate = today()->toDateString();
        $this->maxBookingDate = today()->addDays(7)->toDateString();
    }

    public function chooseMode(string $mode): void
    {
        
        Log::info('BookAppointment chooseMode debug', [
    'mode' => $mode,
    'authenticated' => request()->user() !== null,
    'user_id' => request()->user()?->id,
    'session_id' => request()->session()->getId(),
]);
        if (!in_array($mode, ['doctor', 'date'], true)) {
            return;
        }

        $this->resetValidation();

        $this->mode = $mode;
        $this->doctorId = null;
        $this->availabilityId = null;
        $this->weeklySchedules = [];
        $this->availabilities = [];
        $this->availableDoctors = [];
        $this->doctors = [];
        $this->message = '';

        if ($mode === 'doctor') {
            $cardiologyId = $this->getCardiologySpecialtyId();

            $this->doctors = Doctor::with('user')
                ->where('specialty_id', $cardiologyId)
                ->orderBy('id')
                ->get();

            return;
        }

        // For "Show Me Available Clinics", start with today.
        $this->selectedDate = $this->minBookingDate;

        $this->findAvailableClinics();
    }

    public function chooseDoctor(int $doctorId): void
    {
        $cardiologyId = $this->getCardiologySpecialtyId();

        $doctor = Doctor::with('user', 'weeklySchedules')
            ->where('specialty_id', $cardiologyId)
            ->findOrFail($doctorId);

        $this->mode = 'doctor';
        $this->doctorId = $doctor->id;
        $this->availabilityId = null;
        $this->selectedDate = null;
        $this->availabilities = [];
        $this->message = '';

        $this->weeklySchedules = $doctor->weeklySchedules
            ->where('active', true)
            ->sortBy([
                ['day_of_week', 'asc'],
                ['start_time', 'asc'],
            ])
            ->map(function ($schedule) {
                return [
                    'day' => $this->dayName($schedule->day_of_week),
                    'start' => Carbon::parse($schedule->start_time)
                        ->format('h:i A'),
                    'end' => Carbon::parse($schedule->end_time)
                        ->format('h:i A'),
                    'capacity' => $schedule->capacity,
                    'fee' => number_format(
                        (float) $schedule->consultation_fee,
                        2
                    ),
                ];
            })
            ->values()
            ->all();
    }

    public function updatedSelectedDate(): void
    {
        if (!$this->selectedDate) {
            return;
        }

        $this->validate([
            'selectedDate' => [
                'required',
                'date',
                'after_or_equal:' . $this->minBookingDate,
                'before_or_equal:' . $this->maxBookingDate,
            ],
        ]);

        $this->availabilityId = null;
        $this->message = '';

        if ($this->mode === 'doctor' && $this->doctorId) {
            $this->loadDoctorSessions();

            return;
        }

        if ($this->mode === 'date') {
            $this->findAvailableClinics();
        }
    }

    public function loadDoctorSessions(): void
    {
        if (!$this->doctorId || !$this->selectedDate) {
            return;
        }

        $cardiologyId = $this->getCardiologySpecialtyId();

        $doctor = Doctor::where('specialty_id', $cardiologyId)
            ->findOrFail($this->doctorId);

        $sessionService = app(ClinicSessionService::class);

        $sessionService->generateForDoctorOnDate(
            $doctor,
            Carbon::parse($this->selectedDate)
        );

        $sessions = DoctorAvailability::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('date', $this->selectedDate)
            ->orderBy('start_time')
            ->get();

        $this->availabilities = $sessions
            ->map(fn ($session) => $this->formatSession($session))
            ->values()
            ->all();

        if (empty($this->availabilities)) {
            $this->message =
                'No clinic sessions are available for this doctor on this date.';
        }
    }

    public function findAvailableClinics(): void
    {
        if (!$this->selectedDate) {
            return;
        }

        $date = Carbon::parse($this->selectedDate);
        $cardiologyId = $this->getCardiologySpecialtyId();

        $doctors = Doctor::with('user', 'specialty')
            ->where('specialty_id', $cardiologyId)
            ->whereHas('weeklySchedules', function ($query) use ($date) {
                $query
                    ->where('day_of_week', $date->dayOfWeekIso)
                    ->where('active', true);
            })
            ->orderBy('id')
            ->get();

        $sessionService = app(ClinicSessionService::class);

        $results = [];

        foreach ($doctors as $doctor) {
            $sessions = $sessionService->generateForDoctorOnDate(
                $doctor,
                $date
            );

            if (empty($sessions)) {
                continue;
            }

            $formattedSessions = collect($sessions)
                ->map(fn ($session) => $this->formatSession($session))
                ->values()
                ->all();

            if (empty($formattedSessions)) {
                continue;
            }

            $results[] = [
                'id' => $doctor->id,
                'name' => $doctor->display_name,
                'registration_number' => $doctor->registration_number,
                'sessions' => $formattedSessions,
            ];
        }

        $this->availableDoctors = $results;

        if (empty($results)) {
            $this->message =
                'No clinics are available on this date. Please choose another date.';
        }
    }

    public function selectAvailability(int $availabilityId): void
    {
        $cardiologyId = $this->getCardiologySpecialtyId();

        $availability = DoctorAvailability::with('doctor')
            ->findOrFail($availabilityId);

        abort_unless(
            $availability->doctor->specialty_id === $cardiologyId,
            403
        );

        $date = Carbon::parse($availability->date);

        abort_unless(
            $date->between(
                Carbon::parse($this->minBookingDate),
                Carbon::parse($this->maxBookingDate)
            ),
            422
        );

        if ($this->remainingCapacity($availability) <= 0) {
            $this->addError(
                'availabilityId',
                'This clinic is fully booked.'
            );

            return;
        }

        $this->availabilityId = $availability->id;
        $this->doctorId = $availability->doctor_id;
        $this->selectedDate = $date->toDateString();

        $this->resetValidation();
    }

    public function proceedToPayment(
        AppointmentBookingService $bookingService,
        PaymentService $paymentService
    ): void {
        $this->validate([
            'doctorId' => [
                'required',
                'integer',
                'exists:doctors,id',
            ],

            'selectedDate' => [
                'required',
                'date',
                'after_or_equal:' . $this->minBookingDate,
                'before_or_equal:' . $this->maxBookingDate,
            ],

            'availabilityId' => [
                'required',
                'integer',
                'exists:doctor_availabilities,id',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $patient = Auth::user();

        if (!$patient) {
            abort(401);
        }

        $cardiologyId = $this->getCardiologySpecialtyId();

        $availability = DoctorAvailability::with('doctor')
            ->findOrFail($this->availabilityId);

        abort_unless(
            $availability->doctor_id == $this->doctorId &&
            $availability->doctor->specialty_id == $cardiologyId &&
            Carbon::parse($availability->date)->toDateString() ===
                $this->selectedDate,
            403
        );

        if ($this->remainingCapacity($availability) <= 0) {
            $this->addError(
                'availabilityId',
                'This clinic is now fully booked.'
            );

            return;
        }

        try {
            $appointment = $bookingService->book(
                $patient->id,
                $availability->id,
                $this->reason ?: null
            );

            $payment = $paymentService->initiatePayment(
                $appointment,
                [
                    'name' => $patient->name,
                    'email' => $patient->email,
                ]
            );

            $this->redirect($payment['payment_url']);
        } catch (ValidationException $exception) {
            $this->addError(
                'booking',
                $exception->validator->errors()->first()
            );
        }
    }

    public function backToStart(): void
    {
        $this->mode = null;
        $this->doctorId = null;
        $this->selectedDate = null;
        $this->availabilityId = null;
        $this->doctors = [];
        $this->weeklySchedules = [];
        $this->availabilities = [];
        $this->availableDoctors = [];
        $this->message = '';

        $this->resetValidation();
    }

    private function formatSession(DoctorAvailability $session): array
    {
        $remaining = $this->remainingCapacity($session);

        return [
            'id' => $session->id,
            'date' => Carbon::parse($session->date)
                ->format('d M Y'),
            'start' => Carbon::parse($session->start_time)
                ->format('h:i A'),
            'end' => Carbon::parse($session->end_time)
                ->format('h:i A'),
            'remaining' => $remaining,
            'capacity' => $session->capacity,
            'fee' => number_format(
                (float) $session->consultation_fee,
                2
            ),
            'estimated_minutes' =>
                $session->estimated_consultation_minutes,
        ];
    }

    private function remainingCapacity(
        DoctorAvailability $availability
    ): int {
        $booked = Appointment::query()
            ->where(
                'doctor_availability_id',
                $availability->id
            )
            ->where(function ($query) {
                $query
                    ->whereIn('status', [
                        'confirmed',
                        'arrived',
                        'in_progress',
                    ])
                    ->orWhere(function ($pending) {
                        $pending
                            ->where('status', 'pending_payment')
                            ->whereHas('payments', function ($payment) {
                                $payment
                                    ->where('status', 'pending')
                                    ->where(
                                        'expires_at',
                                        '>',
                                        now()
                                    );
                            });
                    });
            })
            ->count();

        return max(
            0,
            $availability->capacity - $booked
        );
    }

    private function getCardiologySpecialtyId(): int
    {
        return (int) Specialty::where('name', 'Cardiology')
            ->value('id');
    }

    private function dayName(int $day): string
    {
        return match ($day) {
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
            default => 'Unknown',
        };
    }

    public function render()
    {
        return view('livewire.patient.book-appointment');
    }
}