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
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('page_id');
            $table->unsignedBigInteger('page_parent')->nullable()->index();
            $table->string('page_title')->nullable();
            $table->string('page_slug')->nullable();
            $table->string('page_link')->nullable();
            $table->string('page_image')->nullable();
            $table->text('page_desc')->nullable();
            $table->string('page_meta_title')->nullable();
            $table->text('page_meta_keyword')->nullable();
            $table->text('page_meta_desc')->nullable();
            $table->unsignedBigInteger('page_order')->nullable();
            $table->enum('page_status', ['0', '1'])->default('0')->index();
            $table->enum('page_header_status', ['0', '1'])->default('0')->index();
            $table->enum('page_footer_status', ['0', '1'])->default('0')->index();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
