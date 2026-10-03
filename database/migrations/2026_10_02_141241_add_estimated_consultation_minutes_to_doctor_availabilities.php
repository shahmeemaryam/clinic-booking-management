<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_availabilities', function (Blueprint $table) {
            $table->unsignedInteger('estimated_consultation_minutes')
                ->default(15)
                ->after('capacity');
        });
    }

    public function down(): void
    {
        Schema::table('doctor_availabilities', function (Blueprint $table) {
            $table->dropColumn('estimated_consultation_minutes');
        });
    }
};