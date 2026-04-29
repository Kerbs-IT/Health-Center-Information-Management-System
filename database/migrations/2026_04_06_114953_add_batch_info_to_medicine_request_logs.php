<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicine_request_logs', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->default(0)->after('quantity');
            $table->decimal('total_price', 10, 2)->default(0)->after('unit_price');
            // JSON array: [{"batch_id":1,"batch_number":"B001","expiry_date":"2025-04-01","qty_taken":5,"unit_price":10.00}]
            $table->json('batches_used')->nullable()->after('total_price');
        });
    }

    public function down(): void
    {
        Schema::table('medicine_request_logs', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'total_price', 'batches_used']);
        });
    }
};