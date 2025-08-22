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
        Schema::create('pregnancy_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_case_id');
            $table->foreign('medical_record_case_id')->references('id')->on('medical_record_cases')->onDelete('cascade');
            $table->string('patient_name')->nullable();
            $table->string('midwife_name')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('authorized_by_philhealth')->nullable();
            $table->integer('cost_of_pregnancy')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transportation_mode')->nullable();
            $table->string('accompany_person_to_hospital')->nullable();
            $table->string('accompany_through_pregnancy')->nullable();
            $table->string('care_person')->nullable();
            $table->string('emergency_person_name')->nullable();
            $table->string('emergency_person_residency')->nullable();
            $table->string('emergency_person_contact_number')->nullable();
            $table->string('signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pregnancy_plans');
    }
};
