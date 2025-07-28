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
        Schema::rename('medical_record_case', 'medical_record_cases');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('medical_record_cases', 'medical_record_case');
    }
};
