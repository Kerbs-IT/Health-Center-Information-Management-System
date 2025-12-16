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
        Schema::create('medicine_request_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medicine_request_id')->constrained('medicine_requests')->cascadeOnDelete();

            $table->string('patient_name');
            $table->string('medicine_name');
            $table->string('dosage')->nullable();
            $table->integer('quantity');
            $table->string('action');
            $table->unsignedBigInteger('performed_by_id');
            $table->string('performed_by_name');
            $table->timestamp('performed_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_request_logs');
    }
};
