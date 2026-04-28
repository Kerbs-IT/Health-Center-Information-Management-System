<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicine_requests', function (Blueprint $table) {
            // status now: pending | approved | ready_to_pickup | completed | rejected
            $table->timestamp('ready_at')->nullable()->after('approved_at');
            $table->timestamp('dispensed_at')->nullable()->after('ready_at');
            $table->unsignedBigInteger('dispensed_by_id')->nullable()->after('dispensed_at');
        });
    }

    public function down(): void
    {
        Schema::table('medicine_requests', function (Blueprint $table) {
            $table->dropColumn(['ready_at', 'dispensed_at', 'dispensed_by_id']);
        });
    }
};