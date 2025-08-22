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
        Schema::create('prenancy_timeline_records', function (Blueprint $table) {
            $table->id();
            $table-> unsignedBigInteger('prenatal_case_record_id');
            $table-> foreign('prenatal_case_record_id')-> references('id')-> on('prenatal_case_records')-> onDelete('cascade');
            $table-> integer('year');
            $table-> string('type_of_delivery');
            $table-> string('place_of_delivery');
            $table->string('birth_attendant');
            $table-> string('compilation')-> nullable();
            $table-> string('outcome');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prenancy_timeline_records');
    }
};
