<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcategory', function (Blueprint $table) {
            $table->id('fcategory_id');
            $table->string('fcategory_title')->nullable();
            $table->string('fcategory_slug')->nullable();
            $table->unsignedBigInteger('fcategory_order')->default(0);
            $table->enum('fcategory_status', ['0', '1'])
                ->default('1')
                ->index();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcategory');
    }
};
