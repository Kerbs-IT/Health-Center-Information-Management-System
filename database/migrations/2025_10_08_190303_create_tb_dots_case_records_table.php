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
        Schema::create('tb_dots_case_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_case_id');
            $table->foreign('medical_record_case_id')->references('id')->on('medical_record_cases')->onDelete('cascade');
            $table->unsignedBigInteger('health_worker_id')->nullable();
            $table->foreign('health_worker_id')->references('user_id')->on('staff')->onDelete('set null');
            $table->string('patient_name');
            $table->string('type_of_tuberculosis');
            $table->string('type_of_tb_case');
            $table->date('date_of_diagnosis');
            $table->string('name_of_physician')->nullable();
            $table->string('sputum_test_results')->nullable();
            $table->string('treatment_category')->nullable();
            $table->date('date_administered');
            $table->string('side_effect')->nullable();
            $table->string('remarks')->nullable();
            $table->string('outcome')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_dots_case_records');
    }
};
