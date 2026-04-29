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
        Schema::table('family_planning_side_b_records', function (Blueprint $table) {
            $table->boolean('is_final')->default(false)->after('date_of_follow_up_visit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_planning_side_b_records', function (Blueprint $table) {
            //
        });
    }
};
