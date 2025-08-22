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
        Schema::create('donor_names', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pregnancy_plan_id');
            $table->foreign('pregnancy_plan_id')->references('id')-> on('pregnancy_plans')->onDelete('cascade');
            $table-> string('donor_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_names');
    }
};
