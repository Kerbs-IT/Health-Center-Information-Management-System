<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            UPDATE medicines m
            INNER JOIN categories c ON m.category_id = c.category_id
            SET m.category_name = c.category_name
            WHERE m.category_name IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
