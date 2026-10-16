<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('blog', 'bsubcategory_id')) {
            Schema::table('blog', function (Blueprint $table) {
                $table->unsignedBigInteger('bsubcategory_id')
                      ->default(0)
                      ->nullable()
                      ->index()
                      ->after('bcategory_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('blog', 'bsubcategory_id')) {
            Schema::table('blog', function (Blueprint $table) {
                $table->dropColumn('bsubcategory_id');
            });
        }
    }
};
