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
        Schema::create('user_device', function (Blueprint $table) {
            $table->id('user_device_id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_type');
            $table->string('app_version')->nullable();
            $table->string('os_version')->nullable();
            $table->string('device_name')->nullable();
            $table->text('push_notification_id')->nullable();
            $table->string('device_type')->nullable(); // 'Android', 'Ios', 'Web'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_device');
    }
};
