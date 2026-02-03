<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patient', function (Blueprint $table) {
            $table->id('patient_id');
            $table->string('patient_uid')->unique();
            $table->string('patient_fname');
            $table->string('patient_lname');
            $table->string('patient_image')->nullable();
            $table->unsignedTinyInteger('patient_age');
            $table->enum('patient_gender', ['male', 'female', 'other']);
            $table->string('patient_email')->unique();
            $table->string('patient_phone', 15);
            $table->string('patient_password');
            $table->string('patient_marital_status')->nullable();
            $table->string('patient_occupation')->nullable();
            $table->string('patient_blood_group');
            $table->string('patient_blood_pressure')->nullable();
            $table->string('patient_sugar_level')->nullable();
            $table->text('patient_address')->nullable();
            $table->string('patient_city')->nullable();
            $table->string('patient_state')->nullable();
            $table->string('patient_postal_code', 10)->nullable();
            $table->unsignedInteger('patient_order')->default(0);
            $table->enum('patient_status', ['0', '1'])
                ->default('0')
                ->index();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient');
    }
};