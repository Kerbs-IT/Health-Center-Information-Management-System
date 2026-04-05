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
        Schema::create('gc_case_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_case_id');
            $table->foreign('medical_record_case_id')->references('id')->on('medical_record_cases')->onDelete('cascade');
            $table->unsignedBigInteger('health_worker_id')->nullable();
            $table->foreign('health_worker_id')->references('user_id')->on('staff')->onDelete('set null');
            $table->string('blood_pressure')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->string('pulse_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->date('date_of_consultation');
            $table->text('symptoms');
            $table->text('diagnosis');
            $table->text('treatment_plan');
            $table->string('status')->default('Active');
            $table->string('type_of_record')->default('Case Record');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gc_case_records');
    }
};
