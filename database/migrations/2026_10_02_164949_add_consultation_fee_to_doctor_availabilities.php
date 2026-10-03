<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_availabilities', function (Blueprint $table) {
            $table->decimal('consultation_fee', 10, 2)
                ->default(0)
                ->after('estimated_consultation_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('doctor_availabilities', function (Blueprint $table) {
            $table->dropColumn('consultation_fee');
        });
    }
};