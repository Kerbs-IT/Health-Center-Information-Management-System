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
        Schema::table('vaccines', function (Blueprint $table) {
            $table->tinyInteger('max_doses')->unsigned()->default(1)->after('vaccine_acronym')
                ->comment('Minimum 1, Maximum 3');
            $table->enum('status', ['Active', 'Archived'])->default('Active')->after('max_doses');
        });

        // Seed existing vaccines with their known max_doses
        $seedData = [
            'BCG'                  => 1,
            'Hepatitis B'          => 1,
            'PENTA'                => 3,
            'OPV'                  => 3,
            'IPV'                  => 2,
            'PCV'                  => 3,
            'MMR'                  => 2,
            'MCV'                  => 2,
            'TD'                   => 2,
            'Human Papiliomavirus' => 2,
            'Influenza Vaccine'    => 3,
            'Pnuemococcal Vaccine' => 3,
        ];

        foreach ($seedData as $acronym => $maxDoses) {
            DB::table('vaccines')
                ->where('vaccine_acronym', $acronym)
                ->update(['max_doses' => $maxDoses, 'status' => 'Active']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaccines', function (Blueprint $table) {
            $table->dropColumn(['max_doses', 'status']);
        });
    }
};
