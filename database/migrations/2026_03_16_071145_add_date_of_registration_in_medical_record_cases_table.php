<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add as nullable first
        Schema::table('medical_record_cases', function (Blueprint $table) {
            $table->date('date_of_registration')->nullable()->after('patient_id');
        });

        // Step 2: Backfill existing rows
        DB::table('medical_record_cases')->update([
            'date_of_registration' => DB::raw('DATE(created_at)')
        ]);

        // Step 3: Enforce NOT NULL after backfill
        Schema::table('medical_record_cases', function (Blueprint $table) {
            $table->date('date_of_registration')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('medical_record_cases', function (Blueprint $table) {
            $table->dropColumn('date_of_registration');
        });
    }
};
