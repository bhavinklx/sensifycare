<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patient_reminders', function (Blueprint $table) {
            $table->id('patient_reminder_id');
            $table->unsignedBigInteger('patient_id');
            $table->string('title');
            $table->string('date_text');
            $table->string('label')->nullable();
            $table->string('icon_type')->default('general');
            $table->enum('type', ['automated', 'custom'])->default('custom');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->foreign('patient_id')
                ->references('patient_id')
                ->on('patient')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_reminders');
    }
};
