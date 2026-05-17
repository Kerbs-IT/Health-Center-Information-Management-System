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
        //

        // This part is what populates the new table from the old column
        $staffWithAreas = DB::table('staff')
            ->whereNotNull('assigned_area_id')
            ->where('status', 'Active')  // ← only active staff
            ->select('user_id', 'assigned_area_id')
            ->get();

        foreach ($staffWithAreas as $worker) {
            DB::table('staff_area_assignments')->insertOrIgnore([  // ← fix this
                'staff_id'   => $worker->user_id,
                'area_id'    => $worker->assigned_area_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
