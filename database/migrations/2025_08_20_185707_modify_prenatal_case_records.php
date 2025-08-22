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
        Schema::table('prenatal_case_records', function (Blueprint $table) {

            $table->string('blood_pressure', 10)->nullable(); // ex: 120/80
            $table->decimal('temperature', 5, 2)->nullable(); // ex: 36.50
            $table->string('pulse_rate', 20)->nullable(); // ex: "60-100"
            $table->integer('respiratory_rate')->nullable(); // ex: 25
            $table->decimal('height', 5, 2)->nullable(); // ex: 160.50 cm
            $table->decimal('weight', 5, 2)->nullable(); // ex: 55.75 kg
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prenatal_case_records', function (Blueprint $table) {
            //
        });
    }
};
