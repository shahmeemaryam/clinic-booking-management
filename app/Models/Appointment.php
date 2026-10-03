<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_availability_id',
        'queue_number',
        'reason',
        'status',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctorAvailability(): BelongsTo
    {
        return $this->belongsTo(DoctorAvailability::class);
    }
    public function payments(): HasMany
{
    return $this->hasMany(Payment::class);
}
}