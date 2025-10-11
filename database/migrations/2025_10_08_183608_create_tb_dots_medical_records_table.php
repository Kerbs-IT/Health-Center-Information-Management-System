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
        Schema::create('tb_dots_medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_case_id');
            $table->foreign('medical_record_case_id')->references('id')->on('medical_record_cases')->onDelete('cascade');
            $table->unsignedBigInteger('health_worker_id')->nullable();
            $table->foreign('health_worker_id')->references('user_id')->on('staff')->onDelete('set null');
            $table->string('patient_name');
            $table->string('philhealth_id_no')->nullable();
            $table->string('blood_pressure')->nullable();   // usually stored as string e.g. "120/80"
            $table->decimal('temperature', 5, 2)->nullable(); // allows values like 36.75
            $table->integer('pulse_rate')->nullable();     // beats per minute
            $table->integer('respiratory_rate')->nullable(); // breaths per minute
            $table->decimal('height', 5, 2)->nullable();   // meters or cm depending on your standard
            $table->decimal('weight', 6, 2)->nullable();   // kg, e.g. 72.55
            $table->string('type_of_record');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_dots_medical_records');
    }
};
