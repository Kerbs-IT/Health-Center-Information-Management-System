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
        Schema::create('vaccination_case_records', function (Blueprint $table) {
            $table->id();
            $table-> unsignedBigInteger('medical_record_case_id');
            $table->foreign('medical_record_case_id')->references('id')-> on('medical_record_cases')->onDelete('cascade');
            $table-> string('patient_name');
            $table-> string('administered_by');
            $table->date('date_of_vaccination');
            $table->time('time')-> nullable();
            $table->string('vaccine_type');
            $table->integer('dose_number');
            $table-> string('remarks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccination_case_records');
    }
};
