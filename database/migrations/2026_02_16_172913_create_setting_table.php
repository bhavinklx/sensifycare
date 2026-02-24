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
        Schema::create('setting', function (Blueprint $table) {
            $table->increments('setting_id');
            $table->string('setting_label')->nullable();
            $table->string('setting_name')->unique();
            $table->text('setting_value')->nullable();
            $table->string('setting_type')->nullable();
            $table->integer('setting_order')->default(0);
            $table->boolean('setting_status')->default(1);
            $table->string('setting_for')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting');
    }
};
