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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id('medicine_id');
            $table->unsignedBigInteger('category_id');
            $table->string('medicine_name');
            $table->string('dosage');
            $table->integer('stock');
            $table->date('expiry_date')->nullable();
            $table->string('stock_status')->default('In Stock');
            $table->string('expiry_status')->default('Valid');
            $table->integer('min_age_months')->nullable();
            $table->integer('max_age_months')->nullable();


            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
