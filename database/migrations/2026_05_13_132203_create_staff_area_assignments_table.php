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
        Schema::create('staff_area_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');  // references staff.user_id
            $table->unsignedBigInteger('area_id');   // references brgy_unit.id
            $table->timestamps();

            // Ensures one area can only be assigned to ONE worker
            $table->unique('area_id');

            $table->foreign('staff_id')->references('user_id')->on('staff')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('brgy_units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_area_assignments');
    }
};
