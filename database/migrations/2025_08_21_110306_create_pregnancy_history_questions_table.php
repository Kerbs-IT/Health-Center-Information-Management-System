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
        Schema::create('pregnancy_history_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prenatal_case_record_id');
            $table->foreign('prenatal_case_record_id')->references('id')->on('prenatal_case_records')->onDelete('cascade');
            $table-> integer('number_of_children')-> nullable();
            $table->string('answer_1')->nullable();
            $table->string('answer_2')->nullable();
            $table->string('answer_3')->nullable();
            $table->string('answer_4')->nullable();
            $table->string('q2_answer1')->nullable();
            $table->string('q2_answer2')->nullable();
            $table->string('q2_answer3')->nullable();
            $table->string('q2_answer4')->nullable();
            $table->string('q2_answer5')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pregnancy_history_questions');
    }
};
