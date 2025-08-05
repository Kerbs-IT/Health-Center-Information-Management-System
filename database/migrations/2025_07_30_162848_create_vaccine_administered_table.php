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
        Schema::create('vaccine_administered', function (Blueprint $table) {
            $table->id();
            $table-> unsignedBigInteger('vaccination_case_record_id');
            $table-> foreign('vaccination_case_record_id')-> references('id')-> on('vaccination_case_records')-> onDelete('cascade');
            $table-> string('vaccine_type');
            $table-> integer('dose_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccine_administered');
    }
};
