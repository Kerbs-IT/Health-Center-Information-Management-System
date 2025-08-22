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
        Schema::table('prenatal_case_records', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('health_worker_id')->nullable();
            $table->foreign('health_worker_id')->references('user_id')->on('staff')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prenatal_case_records', function (Blueprint $table) {
            //
        });
    }
};
