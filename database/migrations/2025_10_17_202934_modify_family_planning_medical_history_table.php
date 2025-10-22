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
        Schema::rename('family_planning_medical_history','family_planning_medical_histories' );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('family_planning_medical_histories', 'family_planning_medical_history');
    }
};
