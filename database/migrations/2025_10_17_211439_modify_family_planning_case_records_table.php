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
        Schema::table('family_planning_case_records', function (Blueprint $table) {

            $table->string('choosen_method')->nullable();
            $table->string('family_planning_signature_image')->nullable();
            $table->date('family_planning_date_of_acknowledgement')->nullable();
            $table->string('family_planning_acknowlegement_consent_signature_image')->nullable();
            $table->date('family_planning_date_of_acknowledgement_consent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_planning_case_records', function (Blueprint $table) {
            //
        });
    }
};
