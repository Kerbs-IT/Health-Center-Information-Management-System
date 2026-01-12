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
        Schema::table('medicine_requests', function (Blueprint $table) {
            // Store medicine details at time of request
            $table->string('medicine_name')->nullable()->after('medicine_id');
            $table->string('medicine_dosage')->nullable()->after('medicine_name');
            $table->string('medicine_type')->nullable()->after('medicine_dosage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_requests', function (Blueprint $table) {
            $table->dropColumn(['medicine_name', 'medicine_dosage', 'medicine_type']);
        });
    }
};
