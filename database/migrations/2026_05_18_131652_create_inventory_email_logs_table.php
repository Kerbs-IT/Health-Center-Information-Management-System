<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_email_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('alert_type');
            $table->text('reference');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->index(['user_id', 'alert_type', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_email_logs');
    }
};
