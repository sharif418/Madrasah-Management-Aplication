<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Hifz Progress (হিফজ প্রগ্রেস) - মাদরাসার জন্য বিশেষ
        Schema::create('hifz_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->date('date');

            // Sabaq (সবক - নতুন পড়া)
            $table->integer('sabaq_para')->nullable(); // কোন পারা
            $table->string('sabaq_surah')->nullable(); // কোন সূরা
            $table->integer('sabaq_ayat_from')->nullable();
            $table->integer('sabaq_ayat_to')->nullable();
            $table->integer('sabaq_lines')->nullable(); // কত লাইন
            $table->enum('sabaq_quality', ['excellent', 'good', 'average', 'poor'])->nullable();

            // Sabqi (সাবকি - গত দিনের পড়া)
            $table->integer('sabqi_para')->nullable();
            $table->string('sabqi_surah')->nullable();
            $table->enum('sabqi_quality', ['excellent', 'good', 'average', 'poor'])->nullable();

            // Manzil (মনযিল - দোহার/পুরাতন)
            $table->integer('manzil_para_from')->nullable();
            $table->integer('manzil_para_to')->nullable();
            $table->enum('manzil_quality', ['excellent', 'good', 'average', 'poor'])->nullable();

            $table->text('teacher_remarks')->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->timestamps();

            $table->index(['student_id', 'date']);
        });

        // Student Hifz Summary (হিফজ সারাংশ)
        Schema::create('hifz_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->integer('total_para_completed')->default(0); // মোট কত পারা শেষ
            $table->integer('current_para')->nullable(); // বর্তমানে কোন পারায়
            $table->date('hifz_start_date')->nullable();
            $table->date('hifz_complete_date')->nullable();
            $table->boolean('is_hafiz')->default(false);
            $table->enum('status', ['ongoing', 'completed', 'paused'])->default('ongoing');
            $table->timestamps();
        });

        // Kitab/Textbook Progress (কিতাব প্রগ্রেস)
        Schema::create('kitab_list', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // মিশকাত শরীফ, হেদায়া, কাফিয়া
            $table->string('name_en')->nullable();
            $table->string('author')->nullable();
            $table->foreignId('class_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('total_chapters')->nullable();
            $table->integer('total_lessons')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('kitab_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kitab_id')->constrained('kitab_list')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('chapter')->nullable(); // অধ্যায়
            $table->string('lesson')->nullable(); // দরস
            $table->integer('page_from')->nullable();
            $table->integer('page_to')->nullable();
            $table->text('teacher_notes')->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->timestamps();
        });

        // Notices (নোটিশ)
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['general', 'academic', 'exam', 'event', 'urgent'])->default('general');
            $table->enum('audience', ['all', 'students', 'teachers', 'parents', 'staff'])->default('all');
            $table->foreignId('class_id')->nullable()->constrained()->nullOnDelete(); // নির্দিষ্ট ক্লাসের জন্য
            $table->date('publish_date');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_published')->default(true);
            $table->string('attachment')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Events (ইভেন্ট)
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['academic', 'cultural', 'religious', 'sports', 'meeting', 'other'])->default('academic');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('venue')->nullable();
            $table->boolean('is_holiday')->default(false);
            $table->boolean('is_public')->default(true);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        // SMS Logs (SMS লগ)
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->text('message');
            $table->enum('type', ['single', 'bulk'])->default('single');
            $table->enum('purpose', ['attendance', 'fee_reminder', 'result', 'notice', 'emergency', 'other'])->default('other');
            $table->string('gateway_response')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Email Logs (ইমেইল লগ)
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('subject');
            $table->text('body');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('events');
        Schema::dropIfExists('notices');
        Schema::dropIfExists('kitab_progress');
        Schema::dropIfExists('kitab_list');
        Schema::dropIfExists('hifz_summaries');
        Schema::dropIfExists('hifz_progress');
    }
};
