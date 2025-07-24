<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('color_palletes', function (Blueprint $table) {
            $table->id();
            $table -> string('primaryColor')-> default('#FFFFFF');
            $table -> string('secondaryColor') -> default('#065A24');
            $table -> string('tertiaryColor') -> default('#2E8B57');
            $table->timestamps();
        });

        DB::table('color_palletes') -> insert([
            'primaryColor' => '#FFFFFF',
            'secondaryColor' => '#065A24',
            'tertiaryColor' => '#2E8B57',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('color_pallete');
    }
};
