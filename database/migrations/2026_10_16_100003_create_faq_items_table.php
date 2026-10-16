<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('faq', 'faq_items')) {
            Schema::table('faq', function (Blueprint $table) {
                $table->longText('faq_items')->nullable()->after('faq_answer');
            });
        }

        if (!Schema::hasTable('faq_items')) {
            Schema::create('faq_items', function (Blueprint $table) {
                $table->id('faq_item_id');
                $table->unsignedBigInteger('faq_id')->index();
                $table->text('faq_question')->nullable();
                $table->longText('faq_answer')->nullable();
                $table->unsignedInteger('faq_item_order')->default(0);
                $table->timestamps();

                $table->foreign('faq_id')
                      ->references('faq_id')
                      ->on('faq')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_items');
        if (Schema::hasColumn('faq', 'faq_items')) {
            Schema::table('faq', function (Blueprint $table) {
                $table->dropColumn('faq_items');
            });
        }
    }
};
