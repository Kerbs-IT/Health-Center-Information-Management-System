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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->unsignedBigInteger('user_id')-> unique(); // FK
            $table->foreign('user_id')->references('id')-> on('users')-> onDelete('cascade');
            $table->string('street')-> nullable();
            $table->unsignedBigInteger('brgy_id')-> nullable();
            $table->foreign('brgy_id')->references('code')-> on('barangays')-> onDelete('cascade');

            $table->unsignedBigInteger('city_id')-> nullable();
            $table->foreign('city_id')->references('code')-> on('cities')-> onDelete('cascade');

            $table->unsignedBigInteger('province_id')-> nullable();
            $table->foreign('province_id')->references('code')-> on('provinces')-> onDelete('cascade');

            $table->string('region_id')-> nullable();
            $table->foreign('region_id')->references('code')-> on('regions')-> onDelete('cascade');

            $table->string('postal_code')->nullable();
            $table->string('role')-> nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
