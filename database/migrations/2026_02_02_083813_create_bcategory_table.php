<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bcategory', function (Blueprint $table) {
            $table->id('bcategory_id');
            $table->string('bcategory_title')->nullable();
            $table->string('bcategory_slug')->nullable();
            $table->string('bcategory_meta_title')->nullable();
            $table->string('bcategory_meta_keyword')->nullable();
            $table->text('bcategory_meta_desc')->nullable();
            $table->unsignedBigInteger('bcategory_order')->nullable();
            $table->enum('bcategory_status', ['0', '1'])
                ->default('0')
                ->index();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bcategory');
    }
};