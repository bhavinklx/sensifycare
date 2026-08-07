<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('patient_report', function (Blueprint $table) {
            $table->json('ocr_data')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('patient_report', function (Blueprint $table) {
            $table->dropColumn('ocr_data');
        });
    }
};
