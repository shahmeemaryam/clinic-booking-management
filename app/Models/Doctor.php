<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'user_id',
    'specialty_id',
    'registration_number',
    'bio',
];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function availabilities(): HasMany
{
    return $this->hasMany(DoctorAvailability::class);
}

public function weeklySchedules(): HasMany
{
    return $this->hasMany(DoctorWeeklySchedule::class);
}

public function scheduleExceptions(): HasMany
{
    return $this->hasMany(DoctorScheduleException::class);
}
public function getDisplayNameAttribute(): string
{
    return $this->name ?: ($this->user?->name ?? 'Unknown Doctor');
}
}