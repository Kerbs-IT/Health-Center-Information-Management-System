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
        Schema::create('pregnancy_checkups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_case_id');
            $table ->foreign('medical_record_case_id')->references('id')->on('medical_record_cases')->onDelete('cascade');
            $table->string('patient_name');
            $table->unsignedBigInteger('health_worker_id')->nullable();
            $table->foreign('health_worker_id')->references('user_id')->on('staff')->onDelete('no action');
            $table->time('check_up_time')->nullable();
            $table->string('check_up_blood_pressure')->nullable();   // usually stored as string e.g. "120/80"
            $table->decimal('check_up_temperature', 5, 2)->nullable(); // allows values like 36.75
            $table->integer('check_up_pulse_rate')-> nullable();     // beats per minute
            $table->integer('check_up_respiratory_rate') -> nullable(); // breaths per minute
            $table->decimal('check_up_height', 5, 2)->nullable();   // meters or cm depending on your standard
            $table->decimal('check_up_weight', 6, 2)->nullable();   // kg, e.g. 72.55

            $table->string('abdomen_question')->nullable();
            $table->string('abdomen_question_remarks')->nullable();

            $table->string('vaginal_question')->nullable();
            $table->string('vaginal_question_remarks')->nullable();

            $table->string('headache_question')->nullable();
            $table->string('headache_question_remarks')->nullable();

            $table->string('blurry_vission_question')->nullable();
            $table->string('blurry_vission_question_remarks')->nullable();

            $table->string('urination_question')->nullable();
            $table->string('urination_question_remarks')->nullable();

            $table->string('baby_move_question')->nullable();
            $table->string('baby_move_question_remarks')->nullable();

            $table->string('decreased_baby_movement')->nullable();
            $table->string('decreased_baby_movement_remarks')->nullable();

            $table->string('other_symptoms_question')->nullable();
            $table->string('other_symptoms_question_remarks')->nullable();

            $table->string('overall_remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pregnancy_checkups');
    }
};
