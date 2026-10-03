<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE appointments
            MODIFY status ENUM(
                'pending_payment',
                'confirmed',
                'in_progress',
                'cancelled',
                'completed',
                'rescheduled'
            ) NOT NULL DEFAULT 'pending_payment'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE appointments
            MODIFY status ENUM(
                'pending_payment',
                'confirmed',
                'cancelled',
                'completed',
                'rescheduled'
            ) NOT NULL DEFAULT 'pending_payment'
        ");
    }
};