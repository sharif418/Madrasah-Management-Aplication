<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Students (ছাত্র)
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();

            // Basic Information
            $table->string('admission_no')->unique(); // ভর্তি নম্বর
            $table->string('roll_no')->nullable(); // রোল নম্বর
            $table->string('name'); // পুরো নাম (বাংলায়)
            $table->string('name_en')->nullable(); // ইংরেজিতে
            $table->string('father_name');
            $table->string('father_phone')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_name');
            $table->string('mother_phone')->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female'])->default('male');
            $table->string('religion')->default('ইসলাম');
            $table->string('blood_group')->nullable();
            $table->string('nationality')->default('বাংলাদেশী');
            $table->string('birth_certificate_no')->nullable();

            // Contact Information
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('present_address');
            $table->text('permanent_address')->nullable();

            // Guardian Information
            $table->foreignId('guardian_id')->nullable()->constrained()->nullOnDelete();

            // Academic Information
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete();
            $table->date('admission_date');
            $table->string('previous_school')->nullable();
            $table->string('previous_class')->nullable();

            // Hostel/Boarding
            $table->boolean('is_boarder')->default(false); // আবাসিক কি না

            // Health Information
            $table->text('medical_conditions')->nullable();

            // Status
            $table->enum('status', [
                'active',           // বর্তমান ছাত্র
                'inactive',         // নিষ্ক্রিয়
                'transferred',      // বদলি
                'dropped_out',      // ঝরে পড়া
                'passed_out',       // পাস করে বের হয়েছে
                'suspended'         // সাময়িক বহিষ্কার
            ])->default('active');

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['class_id', 'section_id', 'academic_year_id']);
        });

        // Student Documents (ছাত্রের ডকুমেন্ট)
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // birth_certificate, previous_certificate, photo
            $table->string('title');
            $table->string('file_path');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Enrollments (ভর্তি রেকর্ড - প্রতি শিক্ষাবর্ষে)
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->string('roll_no')->nullable();
            $table->date('enrollment_date');
            $table->enum('status', ['active', 'promoted', 'repeated', 'transferred', 'dropped'])->default('active');
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id']);
        });

        // Alumni (প্রাক্তন ছাত্র)
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->year('passing_year');
            $table->string('last_class');
            $table->string('current_occupation')->nullable();
            $table->string('current_address')->nullable();
            $table->text('achievements')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('student_documents');
        Schema::dropIfExists('students');
    }
};
