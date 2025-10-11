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
        Schema::create('tb_dots_maintenance_medicines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tb_dots_case_id');
            $table->foreign('tb_dots_case_id')->references('id')->on('tb_dots_case_records')->onDelete('cascade');
            $table->string('medicine_name');
            $table->string('dosage_n_frequency');
            $table->string('quantity');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_dots_maintenance_medicines');
    }
};
