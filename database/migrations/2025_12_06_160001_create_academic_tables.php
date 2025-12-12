<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Academic Years (শিক্ষাবর্ষ)
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ২০২৪-২০২৫
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Departments (বিভাগ)
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // হিফজ বিভাগ, কিতাব বিভাগ, নাজেরা বিভাগ
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->string('head_name')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Classes (শ্রেণি)
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // নাজেরা, হিফজ, কিতাব-১ম, কিতাব-২য়
            $table->string('name_en')->nullable();
            $table->string('numeric_name')->nullable(); // 1, 2, 3
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Sections (শাখা)
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // ক শাখা, খ শাখা
            $table->integer('capacity')->nullable();
            $table->foreignId('class_teacher_id')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Shifts (শিফট)
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // প্রভাতি, দিবা
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Subjects (বিষয়)
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // কুরআন মাজিদ, হাদিস শরীফ, ফিকহ
            $table->string('name_en')->nullable();
            $table->string('code')->nullable()->unique();
            $table->enum('type', ['theory', 'practical', 'both'])->default('theory');
            $table->integer('full_marks')->default(100);
            $table->integer('pass_marks')->default(33);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Class-Subject Pivot (ক্লাস-বিষয় সম্পর্ক)
        Schema::create('class_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->integer('full_marks')->nullable();
            $table->integer('pass_marks')->nullable();
            $table->boolean('is_optional')->default(false);
            $table->timestamps();

            $table->unique(['class_id', 'subject_id']);
        });

        // Designations (পদবী)
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // মুহতামিম, নায়েবে মুহতামিম, উস্তাদ
            $table->string('name_en')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designations');
        Schema::dropIfExists('class_subject');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('academic_years');
    }
};
