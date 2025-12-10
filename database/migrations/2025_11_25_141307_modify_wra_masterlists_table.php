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
        Schema::table('wra_masterlists', function (Blueprint $table) {
            //
            $table->renameColumn('currently_using_any_FP_method_yes','current_FP_methods');
            $table->string('modern_FP')->nullable();
            $table->string('traditional_FP')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wra_masterlists', function (Blueprint $table) {
            //
        });
    }
};
