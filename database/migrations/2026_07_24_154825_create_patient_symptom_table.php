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
        Schema::create('patient_symptom', function (Blueprint $table) {
            $table->id('patient_symptom_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('symptom_id');
            $table->timestamps();

            $table->foreign('patient_id')->references('patient_id')->on('patient')->onDelete('cascade');
            $table->foreign('symptom_id')->references('symptom_id')->on('symptom')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_symptom');
    }
};
