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
        Schema::create('prenatal_assessment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prenatal_case_record_id');
            $table->foreign('prenatal_case_record_id')->references('id')->on('prenatal_case_records')->onDelete('cascade');
            $table->string('spotting')->nullable()->default('no');
            $table->string('edema')->nullable()->default('no');
            $table->string('severe_headache')->nullable()->default('no');
            $table->string('blumming_vission')->nullable()->default('no');
            $table->string('water_discharge')->nullable()->default('no');
            $table->string('severe_vomitting')->nullable()->default('no');
            $table->string('hx_smoking')->nullable()->default('no');
            $table->string('alchohol_drinker')->nullable()->default('no');
            $table->string('drug_intake')->nullable()->default('no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prenatal_assessment');
    }
};
