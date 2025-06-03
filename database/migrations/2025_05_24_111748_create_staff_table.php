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
        Schema::create('staff', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')-> unique();
            $table->foreign('user_id')->references('id')->on('users')-> onDelete('cascade');
            $table->string('first_name');
            $table-> string('last_name');
            $table-> unsignedBigInteger('assigned_area_id');
            $table->foreign('assigned_area_id')->references('id')->on('brgy_unit')-> onDelete('cascade');
            $table->unsignedBigInteger('address_id')->unique();
            $table-> foreign('address_id')->references('address_id')-> on('addresses')-> onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
