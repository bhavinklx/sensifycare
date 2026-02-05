<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctor', function (Blueprint $table) {
            $table->id('doctor_id');
            $table->string('doctor_uid')->unique();
            $table->string('doctor_fname')->nullable();
            $table->string('doctor_lname')->nullable();
            $table->unsignedTinyInteger('doctor_age');
            $table->enum('doctor_gender', ['male', 'female', 'other']);
            $table->string('doctor_email')->unique();
            $table->string('doctor_phone', 20)->unique();
            $table->string('doctor_password');
            $table->enum('doctor_marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('doctor_blood_group', 5);
            $table->string('doctor_qualification')->nullable();
            $table->string('doctor_designation')->nullable();
            $table->text('doctor_address')->nullable();
            $table->string('doctor_city')->nullable();
            $table->string('doctor_state')->nullable();
            $table->string('doctor_country')->nullable();
            $table->string('doctor_postal_code', 20)->nullable();
            $table->unsignedInteger('doctor_order')->default(0);
            $table->enum('doctor_status', ['0', '1'])
                ->default('0')
                ->index();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor');
    }
};
