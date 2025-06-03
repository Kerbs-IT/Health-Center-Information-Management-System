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
            // Drop the old string column
            $table->dropColumn('department');
        });

        Schema::table('nurses', function (Blueprint $table) {
            // Add the new foreign key column
            $table->unsignedBigInteger('department_id')->nullable(); // or just unsignedInteger

            // Set the foreign key constraint
            $table->foreign('department_id')->references('id')->on('nurse_departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nurses', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            $table->string('department')->nullable();
        });
    }
};
