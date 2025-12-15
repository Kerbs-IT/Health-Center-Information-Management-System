<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vaccination_case_records', function (Blueprint $table) {
            //
            $table->decimal('weight', 5, 2)->nullable();      // e.g. 60.50 (kg)
            $table->decimal('height', 5, 2)->nullable();      // e.g. 165.75 (cm)
            $table->decimal('temperature', 4, 2)->nullable(); // e.g. 36.75 (°C)

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaccination_case_records', function (Blueprint $table) {
            //
        });
    }
};
