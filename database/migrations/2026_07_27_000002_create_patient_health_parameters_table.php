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
        Schema::create('patient_health_parameter', function (Blueprint $table) {
            $table->id('patient_health_parameter_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('health_parameter_id');
            $table->text('health_parameter_answer')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('patient_id')->on('patient')->onDelete('cascade');
            $table->foreign('health_parameter_id')->references('health_parameter_id')->on('health_parameters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_health_parameter');
    }
};
