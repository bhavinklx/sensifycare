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
        Schema::create('health_parameters', function (Blueprint $table) {
            $table->id('health_parameter_id');
            $table->string('health_parameter_name');
            $table->string('health_parameter_question');
            $table->enum('health_parameter_show_type', ['dropdown', 'radio'])->default('dropdown');
            $table->string('health_parameter_option1')->nullable();
            $table->string('health_parameter_option2')->nullable();
            $table->string('health_parameter_option3')->nullable();
            $table->string('health_parameter_option4')->nullable();
            $table->integer('health_parameter_order')->default(0);
            $table->tinyInteger('health_parameter_status')->default(1)->comment('1=Active, 0=Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_parameters');
    }
};
