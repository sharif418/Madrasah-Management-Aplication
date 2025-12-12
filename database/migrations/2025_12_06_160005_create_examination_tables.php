<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Grade System (গ্রেডিং সিস্টেম)
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // A+, A, B, C, D, F
            $table->decimal('min_marks', 5, 2);
            $table->decimal('max_marks', 5, 2);
            $table->decimal('grade_point', 3, 2);
            $table->text('remarks')->nullable(); // অত্যুত্তম, উত্তম
            $table->timestamps();
        });

        // Exam Types (পরীক্ষার ধরণ)
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // প্রথম সাময়িক, দ্বিতীয় সাময়িক, বার্ষিক
            $table->string('name_en')->nullable();
            $table->integer('percentage')->default(100); // Final grade এ কত % count হবে
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Exams (পরীক্ষা)
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // প্রথম সাময়িক পরীক্ষা-২০২৪
            $table->foreignId('exam_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'result_published'])->default('upcoming');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        // Exam Schedules (পরীক্ষার সময়সূচী)
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('full_marks');
            $table->string('room')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'class_id', 'subject_id']);
        });

        // Marks (নম্বর)
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->decimal('written_marks', 5, 2)->nullable(); // লিখিত
            $table->decimal('mcq_marks', 5, 2)->nullable(); // বহুনির্বাচনী
            $table->decimal('practical_marks', 5, 2)->nullable(); // ব্যবহারিক
            $table->decimal('viva_marks', 5, 2)->nullable(); // মৌখিক
            $table->decimal('total_marks', 5, 2)->nullable(); // মোট
            $table->foreignId('grade_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_absent')->default(false); // অনুপস্থিত ছিল কি না
            $table->text('remarks')->nullable();
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id', 'subject_id']);
        });

        // Exam Results (পরীক্ষার ফলাফল)
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_marks', 7, 2)->default(0);
            $table->decimal('total_full_marks', 7, 2)->default(0);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->string('grade')->nullable();
            $table->integer('position')->nullable();
            $table->enum('result_status', ['pass', 'fail', 'promoted', 'not_promoted'])->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('marks');
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_types');
        Schema::dropIfExists('grades');
    }
};
