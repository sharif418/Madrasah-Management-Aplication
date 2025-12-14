<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Update users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('photo')->nullable()->after('phone');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('photo');
            $table->timestamp('last_login_at')->nullable();
        });

        // Teachers (শিক্ষক/উস্তাদ)
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_id')->unique(); // কর্মচারী আইডি
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('religion')->default('ইসলাম');
            $table->string('blood_group')->nullable();
            $table->string('nid')->nullable(); // জাতীয় পরিচয়পত্র
            $table->string('phone');
            $table->string('emergency_phone')->nullable();
            $table->string('email')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();

            // Educational Information
            $table->text('education')->nullable(); // JSON for multiple qualifications
            $table->text('experience')->nullable(); // JSON for experience

            // Employment Information
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained()->nullOnDelete();
            $table->date('joining_date');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->enum('employment_type', ['permanent', 'temporary', 'contractual', 'part_time'])->default('permanent');
            $table->enum('status', ['active', 'inactive', 'on_leave', 'resigned', 'terminated'])->default('active');

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Staff (কর্মচারী - non-teaching)
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('employee_id')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('father_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->string('phone');
            $table->string('nid')->nullable();
            $table->text('address')->nullable();
            $table->string('position'); // দারোয়ান, ক্লিনার, ড্রাইভার, অফিস সহকারী
            $table->foreignId('designation_id')->nullable()->constrained()->nullOnDelete();
            $table->date('joining_date');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'resigned', 'terminated'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Guardians (অভিভাবক)
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('relation'); // পিতা, মাতা, ভাই, চাচা
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('occupation')->nullable();
            $table->string('nid')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('teachers');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'photo', 'status', 'last_login_at']);
        });
    }
};
