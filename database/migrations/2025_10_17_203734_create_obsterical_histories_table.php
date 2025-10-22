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
        Schema::create('obsterical_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id');
            $table->foreign('case_id')->references('id')->on('family_planning_case_records')->onDelete('cascade');

            $table->integer('G');
            $table->integer('P');
            $table->integer('full_term');
            $table->integer('abortion');
            $table->integer('premature');
            $table->integer('living_children');

            $table->date('date_of_last_delivery');
            $table->string('type_of_last_delivery');
            $table->date('date_of_last_delivery_menstrual_period');
            $table->date('date_of_previous_delivery_menstrual_period');
            $table->string('type_of_menstrual');

            $table->string('Dysmenorrhea');
            $table->string('hydatidiform_mole');
            $table->string('ectopic_pregnancy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obsterical_histories');
    }
};
