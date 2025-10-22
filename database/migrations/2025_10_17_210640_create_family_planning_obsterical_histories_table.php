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
        Schema::create('family_planning_obsterical_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id');
            $table->foreign('case_id')->references('id')->on('family_planning_case_records')->onDelete('cascade');

            $table->integer('G')->nullable();
            $table->integer('P')->nullable();
            $table->integer('full_term')->nullable();
            $table->integer('abortion')->nullable();
            $table->integer('premature')->nullable();
            $table->integer('living_children')->nullable();

            $table->date('date_of_last_delivery')->nullable();
            $table->string('type_of_last_delivery')->nullable();
            $table->date('date_of_last_delivery_menstrual_period')->nullable();
            $table->date('date_of_previous_delivery_menstrual_period')->nullable();
            $table->string('type_of_menstrual')->nullable();

            $table->string('Dysmenorrhea')->nullable();
            $table->string('hydatidiform_mole')->nullable();
            $table->string('ectopic_pregnancy')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_planning_obsterical_histories');
    }
};
