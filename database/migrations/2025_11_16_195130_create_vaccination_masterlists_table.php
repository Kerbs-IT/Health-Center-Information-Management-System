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
        Schema::create('vaccination_masterlists', function (Blueprint $table) {
            $table->id();
            $table->string('brgy_name');
            $table->string('midwife');
            $table->unsignedBigInteger('health_worker_id')->nullable();
            $table->foreign('health_worker_id')->references('user_id')->on('staff')->onDelete('set null');
            $table->string('name_of_child');
            $table->unsignedBigInteger('address_id');
            $table->foreign('address_id')->references('id')->on('patient_addresses')->onDelete('cascade');
            $table->string('Address');
            $table->string('sex')->nullable();
            $table->integer('age')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('SE_status')->nullable();
            $table->date('BCG')->nullable();
            $table->date('Hepatitis B')->nullable();
            $table->date('PENTA_1')->nullable();
            $table->date('PENTA_2')->nullable();
            $table->date('PENTA_3')->nullable();
            $table->date('OPV_1')->nullable();
            $table->date('OPV_2')->nullable();
            $table->date('OPV_3')->nullable();
            $table->date('PCV_1')->nullable();
            $table->date('PCV_2')->nullable();
            $table->date('PCV_3')->nullable();
            $table->date('IPV_1')->nullable();
            $table->date('IPV_2')->nullable();
            $table->date('MCV_1')->nullable();
            $table->date('MCV_2')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccination_masterlists');
    }
};
