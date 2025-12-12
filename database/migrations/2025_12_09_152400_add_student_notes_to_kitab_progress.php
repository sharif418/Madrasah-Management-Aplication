<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add student_notes to kitab_progress if not exists
        if (Schema::hasTable('kitab_progress') && !Schema::hasColumn('kitab_progress', 'student_notes')) {
            Schema::table('kitab_progress', function (Blueprint $table) {
                $table->text('student_notes')->nullable()->after('teacher_notes');
                $table->enum('status', ['in_progress', 'completed', 'revision'])->default('in_progress')->after('student_notes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kitab_progress')) {
            Schema::table('kitab_progress', function (Blueprint $table) {
                $table->dropColumn(['student_notes', 'status']);
            });
        }
    }
};
