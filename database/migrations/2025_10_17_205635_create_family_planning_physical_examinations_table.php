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
        Schema::create('family_planning_physical_examinations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id');
            $table->foreign('case_id')->references('id')->on('family_planning_case_records')->onDelete('cascade');

            $table->string('blood_pressure')->nullable();   // usually stored as string e.g. "120/80"
            $table->integer('pulse_rate')->nullable();     // beats per minute
            $table->decimal('height', 5, 2)->nullable();   // meters or cm depending on your standard
            $table->decimal('weight', 6, 2)->nullable();   // kg, e.g. 72.55

            $table->string('skin_type')->nullable();
            $table->string('conjuctiva_type')->nullable();
            $table->string('breast_type')->nullable();
            $table->string('abdomen_type')->nullable();
            $table->string('extremites_type')->nullable();
            $table->string('extremites_UID_type')->nullable();
            $table->string('cervical_abnormalities_type')->nullable();
            $table->string('cervical_consistency_type')->nullable();
            $table->string('uterine_position_type')->nullable();
            $table->integer('uterine_depth_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_planning_physical_examinations');
    }
};
