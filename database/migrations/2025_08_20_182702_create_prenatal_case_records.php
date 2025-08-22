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
        Schema::create('prenatal_case_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_case_id');
            $table->foreign('medical_record_case_id')->references('id')->on('medical_record_cases')->onDelete('cascade');
            $table->string('patient_name');
            $table->integer('G')->nullable();
            $table->integer('P')->nullable();
            $table->integer('T')->nullable();
            $table->integer('premature')->nullable();
            $table->integer('abortion')->nullable();
            $table->integer('living_children')->nullable();
            $table->date('LMP');
            $table->date('expected_delivery')->nullable();
            $table->integer('menarche')->nullable();
            $table->integer('tetanus_toxoid_1')->nullable();
            $table->integer('tetanus_toxoid_2')->nullable();
            $table->integer('tetanus_toxoid_3')->nullable();
            $table->integer('tetanus_toxoid_4')->nullable();
            $table->integer('tetanus_toxoid_5')->nullable();
            $table->string('decision')-> nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prenatal_case_records');
    }
};
