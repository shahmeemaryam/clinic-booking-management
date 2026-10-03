<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });

        // Preserve the names of existing doctors.
        \DB::statement("
            UPDATE doctors d
            INNER JOIN users u ON d.user_id = u.id
            SET d.name = u.name
            WHERE d.name IS NULL
        ");

        Schema::table('doctors', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};