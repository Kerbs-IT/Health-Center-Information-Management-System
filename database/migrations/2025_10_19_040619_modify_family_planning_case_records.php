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
            
            $table->renameColumn('acknowlegement_consent_signature_image', 'acknowledgement_consent_signature_image');
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
