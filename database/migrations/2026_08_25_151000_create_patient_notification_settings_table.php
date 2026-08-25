<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patient_notification_settings', function (Blueprint $table) {
            $table->id('patient_notification_setting_id');
            $table->unsignedBigInteger('patient_id')->unique();
            
            // Health Alerts
            $table->boolean('abnormal_marker_alert')->default(true);
            $table->boolean('report_ready')->default(true);
            $table->boolean('ai_health_insights')->default(true);
            
            // Reminders
            $table->boolean('lab_test_reminders')->default(true);
            $table->boolean('weekly_health_digest')->default(true);
            
            // Content & Updates
            $table->boolean('health_tips_articles')->default(false);
            $table->boolean('app_updates')->default(false);
            $table->boolean('offers_promotions')->default(false);
            
            $table->timestamps();

            $table->foreign('patient_id')
                ->references('patient_id')
                ->on('patient')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_notification_settings');
    }
};
