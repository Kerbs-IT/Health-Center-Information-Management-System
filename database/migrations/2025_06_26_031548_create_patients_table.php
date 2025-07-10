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
        Schema::create('patients', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table ->string('patient_type');
            $table->string('first_name');
            $table->string('middle_nitial')->nullable();
            $table->string('last_name');
            $table->string('full_name')->nullable();
            $table->integer('age')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('sex')->nullable();
            $table->unsignedBigInteger('address_id')->unique();
            $table->foreign('address_id')->references('address_id')->on('addresses')->onDelete('cascade');
            $table->string('civil_status')->nullable();
            $table->integer('contact_number')->nullable();
            $table->string('nationality');
            $table->string('profile_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
