<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add missing columns to teachers table
        Schema::table('teachers', function (Blueprint $table) {
            // Photo field for FileUpload (stored as path, not media library)
            if (!Schema::hasColumn('teachers', 'photo')) {
                $table->string('photo')->nullable()->after('employee_id');
            }

            // Emergency contact fields
            if (!Schema::hasColumn('teachers', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('permanent_address');
            }
            if (!Schema::hasColumn('teachers', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }
            if (!Schema::hasColumn('teachers', 'emergency_contact_relation')) {
                $table->string('emergency_contact_relation')->nullable()->after('emergency_contact_phone');
            }

            // Specialization field
            if (!Schema::hasColumn('teachers', 'specialization')) {
                $table->text('specialization')->nullable()->after('experience');
            }

            // Documents field (JSON for multiple file paths)
            if (!Schema::hasColumn('teachers', 'documents')) {
                $table->json('documents')->nullable()->after('specialization');
            }

            // Resignation date
            if (!Schema::hasColumn('teachers', 'resignation_date')) {
                $table->date('resignation_date')->nullable()->after('status');
            }
        });

        // Add missing columns to staff table
        Schema::table('staff', function (Blueprint $table) {
            if (!Schema::hasColumn('staff', 'photo')) {
                $table->string('photo')->nullable()->after('employee_id');
            }
            if (!Schema::hasColumn('staff', 'mother_name')) {
                $table->string('mother_name')->nullable()->after('father_name');
            }
            if (!Schema::hasColumn('staff', 'marital_status')) {
                $table->string('marital_status')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('staff', 'blood_group')) {
                $table->string('blood_group')->nullable()->after('marital_status');
            }
            if (!Schema::hasColumn('staff', 'religion')) {
                $table->string('religion')->default('ইসলাম')->after('blood_group');
            }
            if (!Schema::hasColumn('staff', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('staff', 'present_address')) {
                $table->text('present_address')->nullable()->after('address');
            }
            if (!Schema::hasColumn('staff', 'permanent_address')) {
                $table->text('permanent_address')->nullable()->after('present_address');
            }
            if (!Schema::hasColumn('staff', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('permanent_address');
            }
            if (!Schema::hasColumn('staff', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }
            if (!Schema::hasColumn('staff', 'emergency_contact_relation')) {
                $table->string('emergency_contact_relation')->nullable()->after('emergency_contact_phone');
            }
            if (!Schema::hasColumn('staff', 'education')) {
                $table->json('education')->nullable()->after('emergency_contact_relation');
            }
            if (!Schema::hasColumn('staff', 'experience')) {
                $table->json('experience')->nullable()->after('education');
            }
            if (!Schema::hasColumn('staff', 'department_id')) {
                $table->foreignId('department_id')->nullable()->after('designation_id');
            }
            if (!Schema::hasColumn('staff', 'employment_type')) {
                $table->string('employment_type')->default('permanent')->after('basic_salary');
            }
            if (!Schema::hasColumn('staff', 'resignation_date')) {
                $table->date('resignation_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('staff', 'documents')) {
                $table->json('documents')->nullable()->after('resignation_date');
            }
            if (!Schema::hasColumn('staff', 'notes')) {
                $table->text('notes')->nullable()->after('documents');
            }
        });

        // Add is_active to departments if missing
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('order');
            }
        });

        // Add is_active to designations if missing
        Schema::table('designations', function (Blueprint $table) {
            if (!Schema::hasColumn('designations', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('order');
            }
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn([
                'photo',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relation',
                'specialization',
                'documents',
                'resignation_date'
            ]);
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn([
                'photo',
                'mother_name',
                'marital_status',
                'blood_group',
                'religion',
                'email',
                'present_address',
                'permanent_address',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relation',
                'education',
                'experience',
                'department_id',
                'employment_type',
                'resignation_date',
                'documents',
                'notes'
            ]);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('designations', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
