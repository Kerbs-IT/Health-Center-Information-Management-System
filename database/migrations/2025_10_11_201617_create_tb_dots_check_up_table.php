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
        Schema::create('tb_dots_check_ups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_case_id');
            $table->foreign('medical_record_case_id')->references('id')->on('medical_record_cases')->onDelete('cascade');
            $table->unsignedBigInteger('health_worker_id')->nullable();
            $table->foreign('health_worker_id')->references('user_id')->on('staff')->onDelete('set null');
            $table->string('patient_name');
            $table->date('date_of_visit');
            $table->string('blood_pressure')->nullable();   // usually stored as string e.g. "120/80"
            $table->decimal('temperature', 5, 2)->nullable(); // allows values like 36.75
            $table->integer('pulse_rate')->nullable();     // beats per minute
            $table->integer('respiratory_rate')->nullable(); // breaths per minute
            $table->decimal('height', 5, 2)->nullable();   // meters or cm depending on your standard
            $table->decimal('weight', 6, 2)->nullable();   // kg, e.g. 72.55
            $table->string('adherence_of_treatment');
            $table->string('side_effect')->nullable();
            $table->string('progress_note')->nullable();
            $table->string('sputum_test_result')->nullable();
            $table->string('treatment_phase')->nullable();
            $table->string('outcome')->nullable();
            $table->string('type_of_record')->default('Follow-Up Check-Up');
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_dots_check_ups');
    }
};
