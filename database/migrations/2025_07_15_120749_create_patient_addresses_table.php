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
        Schema::create('patient_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade')->unique(); // enforce 1:1
            $table->string('house_number');
            $table->string('street')->nullable();
            $table->string('purok');
            $table->string('barangay')->default('Hugo Perez');
            $table->string('city')->default('Trece Martires');
            $table->string('province')->default('Cavite');
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_addresses');
    }
};
