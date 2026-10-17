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
        Schema::table('faq', function (Blueprint $table) {
            if (!Schema::hasColumn('faq', 'blog_id')) {
                $table->unsignedBigInteger('blog_id')->nullable()->index()->after('fcategory_id');
                $table->foreign('blog_id')->references('blog_id')->on('blog')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faq', function (Blueprint $table) {
            if (Schema::hasColumn('faq', 'blog_id')) {
                $table->dropForeign(['blog_id']);
                $table->dropColumn('blog_id');
            }
        });
    }
};
