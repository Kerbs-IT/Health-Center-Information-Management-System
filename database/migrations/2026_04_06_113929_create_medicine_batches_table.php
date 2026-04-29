<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicine_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medicine_id');
            $table->string('batch_number')->nullable();
            $table->integer('quantity');
            $table->integer('initial_quantity');
            $table->decimal('price', 10, 2)->default(0);
            $table->date('manufactured_date')->nullable();
            $table->date('expiry_date');
            $table->string('expiry_status')->default('Valid'); // Valid, Expiring Soon, Expired
            $table->timestamps();

            $table->foreign('medicine_id')
                  ->references('medicine_id')
                  ->on('medicines')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_batches');
    }
};