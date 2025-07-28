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
        Schema::rename('vaccination_medical_record', 'vaccination_medical_records' );
        Schema::table('vaccination_medical_records', function (Blueprint $table) {
            $table-> string('type_of_record');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('vaccination_medical_records', 'vaccination_medical_record');
    }
};
