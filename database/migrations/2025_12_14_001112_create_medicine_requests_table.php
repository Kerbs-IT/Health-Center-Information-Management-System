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
        Schema::create('medicine_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patients_id');
            $table->unsignedBigInteger('medicine_id');
            $table->integer('quantity_requested');
            $table->string('reason')->nullable();
            $table->enum('status',['pending', 'approved', 'rejected', 'completed']);
            $table->timestamp('requested_at')->useCurrent();
            $table->unsignedBigInteger('approved_by_id')->nullable();
            $table->string('approved_by_type')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->foreign('patients_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('medicine_id')->references('medicine_id')->on('medicines')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_requests');
    }
};
