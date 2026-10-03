<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_weekly_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('doctor_id')
                ->constrained('doctors')
                ->cascadeOnDelete();

            // ISO day: 1 = Monday, 7 = Sunday.
            $table->unsignedTinyInteger('day_of_week');

            $table->time('start_time');
            $table->time('end_time');

            $table->unsignedInteger('capacity')->default(20);

            $table->unsignedInteger('estimated_consultation_minutes')
                ->default(15);

            $table->boolean('active')->default(true);

            $table->timestamps();

           $table->unique(
    [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
    ],
    'doctor_weekly_sched_unique'
);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_weekly_schedules');
    }
};