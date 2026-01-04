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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type'); // 'appointment_reminder', 'general', etc.
            $table->string('title');
            $table->text('message');
            $table->string('appointment_type')->nullable(); // 'vaccination', 'prenatal', etc.
            $table->date('appointment_date')->nullable();
            $table->time('appointment_time')->nullable();
            $table->unsignedBigInteger('medical_record_case_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Indexes for faster queries
            $table->index(['user_id', 'is_read']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
