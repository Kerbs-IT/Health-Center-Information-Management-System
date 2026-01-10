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
        Schema::table('medicine_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('patients_id')->nullable()->change();
            $table->unsignedBigInteger('user_id')->nullable()->after('patients_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn(['user_id']);


            $table->unsignedBigInteger('patients_id')->nullable(false)->change();
        });
    }
};
