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
        Schema::rename('prenatal_assessment', "prenatal_assessments");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('prenatal_assessments', "prenatal_assessment");
    }
};
