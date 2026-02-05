<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banner', function (Blueprint $table) {
            $table->increments('banner_id');
            $table->string('banner_title')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('banner_text')->nullable();
            $table->string('banner_text1')->nullable();
            $table->unsignedBigInteger('banner_order')->nullable();
            $table->enum('banner_status', ['0','1'])->default('0');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banner');
    }
};