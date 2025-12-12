<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * grades টেবিলে is_active কলাম যোগ করা
     */
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            if (!Schema::hasColumn('grades', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('remarks');
            }
        });

        // Update ExamSchedule table to add pass_marks if not exists
        Schema::table('exam_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_schedules', 'pass_marks')) {
                $table->integer('pass_marks')->default(33)->after('full_marks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            if (Schema::hasColumn('grades', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });

        Schema::table('exam_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('exam_schedules', 'pass_marks')) {
                $table->dropColumn('pass_marks');
            }
        });
    }
};
