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
            $table->integer('reserved_quantity')->nullable()->after('quantity_requested');
            $table->json('batches_snapshot')->nullable()->after('reserved_quantity');
            $table->timestamp('reserved_at')->nullable()->after('ready_at');
            $table->timestamp('cancelled_at')->nullable()->after('reserved_at');
            $table->string('cancellation_reason')->nullable()->after('cancelled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_requests', function (Blueprint $table) {
            //
        });
    }
};
