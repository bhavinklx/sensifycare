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
        Schema::create('symptom', function (Blueprint $table) {
            $table->id('symptom_id');
            $table->string('symptom_name');
            $table->string('symptom_image')->nullable();
            $table->text('symptom_desc')->nullable();
            $table->unsignedInteger('symptom_order')->default(0);
            $table->enum('symptom_status', ['0', '1'])->default('1')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symptom');
    }
};
