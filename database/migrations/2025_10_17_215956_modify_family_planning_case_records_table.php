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
            $table->renameColumn('family_planning_signature_image', 'signature_image');
            $table->renameColumn('family_planning_date_of_acknowledgement', 'date_of_acknowledgement');
            $table->renameColumn('family_planning_acknowlegement_consent_signature_image', 'acknowlegement_consent_signature_image');
            $table->renameColumn('family_planning_date_of_acknowledgement_consent', 'date_of_acknowledgement_consent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_planning_case_records', function (Blueprint $table) {
            $table->renameColumn('signature_image', 'family_planning_signature_image');
            $table->renameColumn('date_of_acknowledgement', 'family_planning_date_of_acknowledgement');
            $table->renameColumn('acknowledgement_consent_signature_image', 'family_planning_acknowledgement_consent_signature_image');
            $table->renameColumn('date_of_acknowledgement_consent', 'family_planning_date_of_acknowledgement_consent');
        });
    }
};
