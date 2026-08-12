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
        Schema::table('patient_report', function (Blueprint $table) {
            $table->string('report_title')->nullable()->after('ocr_data');
            $table->integer('score')->nullable()->after('report_title');
            $table->integer('markers_count')->nullable()->after('score');
            $table->integer('abnormal_count')->nullable()->after('markers_count');
            $table->integer('ok_count')->nullable()->after('abnormal_count');
            $table->integer('pages_count')->nullable()->after('ok_count');
            $table->string('report_quality')->nullable()->after('pages_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_report', function (Blueprint $table) {
            $table->dropColumn([
                'report_title',
                'score',
                'markers_count',
                'abnormal_count',
                'ok_count',
                'pages_count',
                'report_quality'
            ]);
        });
    }
};
