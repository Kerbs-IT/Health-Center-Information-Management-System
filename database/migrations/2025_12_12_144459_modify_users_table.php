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
        Schema::table('users', function (Blueprint $table) {
            // Add patient-specific fields if they don't exist
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->after('email');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->after('first_name');
            }
            if (!Schema::hasColumn('users', 'middle_initial')) {
                $table->string('middle_initial', 5)->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('middle_initial');
            }
            if (!Schema::hasColumn('users', 'contact_number')) {
                $table->string('contact_number', 20)->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('contact_number');
            }
            if (!Schema::hasColumn('users', 'patient_type')) {
                $table->enum('patient_type', [
                    'vaccination',
                    'prenatal',
                    'tb-dots',
                    'senior-citizen',
                    'family-planning'
                ])->after('email');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['patient', 'staff', 'nurse', 'admin'])
                    ->default('patient')
                    ->after('patient_type');
            }

            // Add patient_record_id for binding (without foreign key constraint yet)
            if (!Schema::hasColumn('users', 'patient_record_id')) {
                $table->unsignedBigInteger('patient_record_id')->nullable()->after('role');
                $table->index('patient_record_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'patient_record_id',
                'username',
                'first_name',
                'last_name',
                'middle_initial',
                'date_of_birth',
                'contact_number',
                'address',
                'patient_type',
                'role'
            ]);
        });
    }
};
