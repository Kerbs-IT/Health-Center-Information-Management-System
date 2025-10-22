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
        Schema::create('risk_for_sexually_transmitted_infections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id');
            $table->foreign('case_id')->references('id')->on('family_planning_case_records')->onDelete('cascade');

            $table->string('infection_abnormal_discharge_from_genital_area')->nullable();
            $table->string('origin_of_abnormal_discharge')->nullable();
            $table->string('scores_or_ulcer')->nullable();
            $table->string('pain_or_burning_sensation')->nullable();
            $table->string('history_of_sexually_transmitted_infection')->nullable();
            $table->string('sexually_transmitted_disease')->nullable();

            // IV. RISKS FOR VIOLENCE AGAINTS WOMEN (VAW)
            $table->string('history_of_domestic_violence_of_VAW')->nullable();
            $table->string('unpleasant_relationship_with_partner')->nullable();
            $table->string('partner_does_not_approve')->nullable();
            $table->string('referred_to')->nullable();
            $table->string('reffered_to_others')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_for_sexually_transmitted_infections');
    }
};
