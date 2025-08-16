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
        Schema::table('vaccine_administereds', function (Blueprint $table) {
            $table-> unsignedBigInteger('vaccine_id')->nullable();
            $table-> foreign('vaccine_id')-> references('id')-> on('vaccines')-> onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaccine_administereds', function (Blueprint $table) {
            //
        });
    }
};
