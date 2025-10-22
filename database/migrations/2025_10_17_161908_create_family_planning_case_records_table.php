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
        Schema::create('family_planning_case_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_case_id');
            $table->foreign('medical_record_case_id')->references('id')->on('medical_record_cases')->onDelete('cascade');
            $table->unsignedBigInteger('health_worker_id')->nullable();
            $table->foreign('health_worker_id')->references('user_id')->on('staff')->onDelete('set null');
            $table->string('client_id')->nullable();
            $table->string('philhealth_no')->nullable();
            $table->string('NHTS')->nullable();

            $table->string('client_name');
            $table->date('client_date_of_birth')->nullable();
            $table->integer('client_age')->nullable();
            $table->string('occupation')->nullable();
            $table->string('client_address')->nullable();
            $table->string('client_contact_number')->nullable();
            $table->string('client_civil_status')->nullable();
            $table->string('client_religion')->nullable();

            // spouse
            $table->string('spouse_lname')->nullable();
            $table->string('spouse_fname')->nullable();
            $table->string('spouse_MI')->nullable();
            $table->date('spouse_date_of_birth')->nullable();
            $table->integer('spouse_age')->nullable();
            $table->string('spouse_occupation')->nullable();

            $table->integer('number_of_living_children')->nullable();
            $table->string('plan_to_have_more_children')->nullable();
            $table->bigInteger('average_montly_income')->nullable();
            $table->string('type_of_patient');
            $table->string('new_acceptor_reason_for_FP')->nullable();
            $table->string('current_user_reason_for_FP')->nullable();
            $table->string('current_method_reason')->nullable();
            $table->string('previously_used_method')->nullable();
            $table->string('type_of_record')->default('Case Record');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_planning_case_records');
    }
};
