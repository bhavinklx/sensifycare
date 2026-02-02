<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog', function (Blueprint $table) {
            $table->id('blog_id');
            $table->unsignedBigInteger('bcategory_id')->nullable()->index();
            $table->string('blog_title')->nullable();
            $table->string('blog_slug')->nullable();
            $table->date('blog_date')->nullable();
            $table->string('blog_image')->nullable();
            $table->text('blog_short_desc')->nullable();
            $table->text('blog_desc')->nullable();
            $table->text('blog_meta_title')->nullable();
            $table->text('blog_meta_keyword')->nullable();
            $table->text('blog_meta_desc')->nullable();
            $table->string('blog_canonical')->nullable();
            $table->unsignedInteger('blog_order')->default(0);
            $table->enum('blog_status', ['0', '1'])
                ->default('0')
                ->index();
            $table->enum('blog_popular_status', ['0', '1'])
                ->default('0')
                ->nullable()
                ->index();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Optional foreign key (uncomment if needed)
            // $table->foreign('bcategory_id')
            //       ->references('bcategory_id')
            //       ->on('bcategory')
            //       ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog');
    }
};

