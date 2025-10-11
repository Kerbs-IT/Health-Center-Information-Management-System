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
        Schema::create('senior_citizen_maintenance_meds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('senior_citizen_case_id');
            $table->foreign('senior_citizen_case_id')->references('id')->on('senior_citizen_case_records')->onDelete('cascade');
            $table->string('maintenance_medication');
            $table->string('dosage_n_frequency');
            $table->string('duration');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('senior_citizen_maintenance_meds');
    }
};
