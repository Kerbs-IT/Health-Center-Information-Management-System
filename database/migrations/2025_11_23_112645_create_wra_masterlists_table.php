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
        Schema::create('wra_masterlists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('health_worker_id')->nullable();
            $table->foreign('health_worker_id')->references('user_id')->on('staff')->onDelete('set null');
            $table->unsignedBigInteger('address_id');
            $table->foreign('address_id')->references('id')->on('patient_addresses')->onDelete('cascade');
            $table->unsignedBigInteger('medical_record_case_id');
            $table->foreign('medical_record_case_id')->references('id')->on('medical_record_cases')->onDelete('cascade');
            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->string('house_hold_number')->nullable();
            $table->string('name_of_wra');
            $table->string('address');
            $table->integer('age')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('SE_status')->nullable();

            $table->string('plan_to_have_more_children_yes')->nullable();
            $table->string('plan_to_have_more_children_no')->nullable();

            $table->string('currently_using_any_FP_method_yes')->nullable();
            $table->string('currently_using_any_FP_method_no')->nullable();
            $table->string('shift_to_modern_method')->nullable();

            $table->string('wra_with_MFP_unmet_need')->nullable();
            $table->string('wra_accept_any_modern_FP_method')->nullable();

            $table->string('selected_modern_FP_method')->nullable();
            $table->date('date_when_FP_method_accepted')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wra_masterlists');
    }
};
