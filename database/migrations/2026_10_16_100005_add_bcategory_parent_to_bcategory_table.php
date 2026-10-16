<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('bcategory', 'bcategory_parent')) {
            Schema::table('bcategory', function (Blueprint $table) {
                $table->unsignedBigInteger('bcategory_parent')
                      ->default(0)
                      ->nullable()
                      ->index()
                      ->after('bcategory_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bcategory', 'bcategory_parent')) {
            Schema::table('bcategory', function (Blueprint $table) {
                $table->dropColumn('bcategory_parent');
            });
        }
    }
};
