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
        // Step 1: Drop foreign key
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Step 2: Make column nullable
        Schema::table('patients', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        // Step 3: Re-add foreign key
        Schema::table('patients', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null'); // or 'cascade'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            //
        });
    }
};
