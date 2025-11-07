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
            //
            $table->string('type_of_record')->default('Family Planning Client Assessment Record - Side B')->change();
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
