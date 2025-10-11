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
        Schema::create('senior_citizen_case_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_case_id');
            $table->foreign('medical_record_case_id')->references('id')->on('medical_record_cases')->onDelete('cascade');
            $table->unsignedBigInteger('health_worker_id')-> nullable();
            $table->foreign('health_worker_id')->references('user_id')->on('staff')->onDelete("set null");
            $table->string('patient_name');
            $table->string( 'existing_medical_condition')->nullable();
            $table->string('alergies')-> nullable();
            $table->string('prescribe_by_nurse')->nullable();
            $table->string('remarks');
            $table->string('type_of_record');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('senior_citizen_case_records');
    }
};
