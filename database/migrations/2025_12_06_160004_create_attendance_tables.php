<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Student Attendance (ছাত্র উপস্থিতি)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', [
                'present',      // উপস্থিত
                'absent',       // অনুপস্থিত
                'late',         // দেরিতে আসা
                'half_day',     // অর্ধ দিবস
                'leave'         // ছুটিতে
            ])->default('present');
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'date']);
            $table->index(['class_id', 'section_id', 'date']);
        });

        // Teacher/Staff Attendance (শিক্ষক/কর্মচারী উপস্থিতি)
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->enum('attendee_type', ['teacher', 'staff']);
            $table->unsignedBigInteger('attendee_id'); // teacher_id or staff_id
            $table->date('date');
            $table->enum('status', [
                'present',
                'absent',
                'late',
                'half_day',
                'leave'
            ])->default('present');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['attendee_type', 'attendee_id', 'date']);
        });

        // Leave Types (ছুটির প্রকার)
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // অসুস্থতাজনিত, ব্যক্তিগত, বার্ষিক
            $table->integer('days_allowed')->default(0); // বছরে কত দিন পাবে
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Leave Applications (ছুটির আবেদন)
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();
            $table->enum('applicant_type', ['teacher', 'staff', 'student']);
            $table->unsignedBigInteger('applicant_id');
            $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('total_days');
            $table->text('reason');
            $table->string('document_path')->nullable(); // supporting document
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_applications');
        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('staff_attendances');
        Schema::dropIfExists('attendances');
    }
};
