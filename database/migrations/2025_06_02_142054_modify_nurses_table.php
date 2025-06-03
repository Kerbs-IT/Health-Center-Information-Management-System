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
        Schema::table('nurses', function (Blueprint $table) {
            //
            $table -> integer('age')-> nullable();
            $table -> date('date_of_birth') -> nullable();
            $table -> string('sex') -> nullable();
            $table -> string('civil_status') -> nullable();
            $table -> integer('contact_number')-> nullable();
            $table -> string('nationality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nurses', function (Blueprint $table) {
            //
        });
    }
};
