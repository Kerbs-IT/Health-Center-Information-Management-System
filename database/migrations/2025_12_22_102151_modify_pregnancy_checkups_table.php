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
        Schema::table('pregnancy_checkups', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['health_worker_id']);

            // Add the new foreign key with SET NULL
            $table->foreign('health_worker_id')
                ->references('user_id')
                ->on('staff')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pregnancy_checkups', function (Blueprint $table) {
            $table->dropForeign(['health_worker_id']);

            // Restore the original NO ACTION constraint
            $table->foreign('health_worker_id')
                ->references('user_id')
                ->on('staff')
                ->onDelete('no action');
        });
    }
};
