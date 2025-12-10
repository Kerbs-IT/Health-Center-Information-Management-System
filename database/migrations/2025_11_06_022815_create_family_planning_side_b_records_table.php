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
        Schema::create('family_planning_side_b_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_case_id');
            $table->foreign('medical_record_case_id')->references('id')->on('medical_record_cases')->onDelete('cascade');
            $table->unsignedBigInteger('health_worker_id')->nullable();
            $table->foreign('health_worker_id')->references('user_id')->on('staff')->onDelete('set null');
            $table->date('date_of_visit')->nullable();
            $table->string('medical_findings')->nullable();
            $table->string('method_accepted')->nullable();
            $table->string('signature_of_the_provider')->nullable();
            $table->date('date_of_follow_up_visit')->nullable();
            $table->string('baby_Less_than_six_months_question')->nullable();
            $table->string('sexual_intercouse_or_mesntrual_period_question')->nullable();
            $table->string('baby_last_4_weeks_question')->nullable();
            $table->string('menstrual_period_in_seven_days_question')->nullable();
            $table->string('miscarriage_or_abortion_question')->nullable();
            $table->string('contraceptive_question')->nullable();
            $table->string('status')->default('Pending');
            $table->string('type_of_record')->default('Family Planning Client Assessment Record');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_planning_side_b_records');
    }
};
