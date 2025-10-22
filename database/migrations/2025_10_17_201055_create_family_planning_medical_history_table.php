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
        Schema::create('family_planning_medical_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id');
            $table->foreign('case_id')->references('id')->on('family_planning_case_records')->onDelete('cascade');
            $table->string('severe_headaches_migraine')->nullable();
            $table->string('history_of_stroke')->nullable();
            $table->string('non_traumatic_hemtoma')->nullable();
            $table->string('history_of_breast_cancer')->nullable();

            $table->string('severe_chest_pain')->nullable();
            $table->string('cough')->nullable();
            $table->string('jaundice')->nullable();
            $table->string('unexplained_vaginal_bleeding')->nullable();
            $table->string('abnormal_vaginal_discharge')->nullable();

            $table->string('abnormal_phenobarbital')->nullable();
            $table->string('smoker')->nullable();
            $table->string('with_dissability')->nullable();
            $table->string('if_with_dissability_specification')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_planning_medical_history');
    }
};
