<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add tajweed/qirat fields to hifz_progress
        if (Schema::hasTable('hifz_progress') && !Schema::hasColumn('hifz_progress', 'tajweed_lesson')) {
            Schema::table('hifz_progress', function (Blueprint $table) {
                $table->string('tajweed_lesson')->nullable()->after('teacher_remarks');
                $table->enum('tajweed_quality', ['excellent', 'good', 'average', 'poor'])->nullable()->after('tajweed_lesson');
                $table->string('qirat_surah')->nullable()->after('tajweed_quality');
                $table->enum('qirat_quality', ['excellent', 'good', 'average', 'poor'])->nullable()->after('qirat_surah');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hifz_progress')) {
            Schema::table('hifz_progress', function (Blueprint $table) {
                $table->dropColumn(['tajweed_lesson', 'tajweed_quality', 'qirat_surah', 'qirat_quality']);
            });
        }
    }
};
