<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faq', function (Blueprint $table) {
            $table->id('faq_id');
            $table->unsignedBigInteger('fcategory_id')->nullable()->index();
            $table->text('faq_question')->nullable();
            $table->longText('faq_answer')->nullable();
            $table->unsignedInteger('faq_order')->default(0);
            $table->enum('faq_status', ['0', '1'])
                ->default('1')
                ->index();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Foreign key to fcategory table (optional cascade)
            $table->foreign('fcategory_id')
                  ->references('fcategory_id')
                  ->on('fcategory')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faq');
    }
};
