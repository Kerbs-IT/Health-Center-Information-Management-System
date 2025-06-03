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
        Schema::create('nurses', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')-> on('users')-> onDelete('cascade');
            $table->string('first_name') -> nullable();
            $table->string('last_name')->nullable();
            $table->string('department');
            $table->unsignedBigInteger('address_id');
            $table-> foreign('address_id')->references('address_id')-> on('addresses')-> onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurses');
    }
};
