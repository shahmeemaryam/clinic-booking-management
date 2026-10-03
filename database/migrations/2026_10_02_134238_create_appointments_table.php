<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('patient_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('doctor_availability_id')
                ->constrained('doctor_availabilities')
                ->cascadeOnDelete();

            $table->unsignedInteger('queue_number');

            $table->string('reason')->nullable();

            $table->enum('status', [
                'pending_payment',
                'confirmed',
                'cancelled',
                'completed',
                'rescheduled',
            ])->default('pending_payment');

            $table->timestamps();

            // A queue number can only be used once within a clinic session.
            $table->unique([
                'doctor_availability_id',
                'queue_number',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};