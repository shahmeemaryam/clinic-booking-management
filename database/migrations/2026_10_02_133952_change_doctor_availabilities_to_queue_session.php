<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_availabilities', function (Blueprint $table) {
            $table->unsignedInteger('capacity')
                ->default(20)
                ->after('end_time');

            $table->dropColumn('slot_duration');
        });
    }

    public function down(): void
    {
        Schema::table('doctor_availabilities', function (Blueprint $table) {
            $table->unsignedInteger('slot_duration')
                ->default(15)
                ->after('end_time');

            $table->dropColumn('capacity');
        });
    }
};