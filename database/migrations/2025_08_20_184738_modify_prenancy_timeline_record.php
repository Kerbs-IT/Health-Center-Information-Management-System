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
        Schema::rename('prenancy_timeline_records', 'pregnancy_timeline_records');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('pregnancy_timeline_records', 'prenancy_timeline_records');
    }
};
