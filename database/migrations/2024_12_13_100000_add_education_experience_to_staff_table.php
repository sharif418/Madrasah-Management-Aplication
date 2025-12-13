<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            // New personal fields
            if (!Schema::hasColumn('staff', 'mother_name')) {
                $table->string('mother_name')->nullable()->after('father_name');
            }
            if (!Schema::hasColumn('staff', 'emergency_phone')) {
                $table->string('emergency_phone')->nullable()->after('phone');
            }

            // Replace single address with present/permanent
            if (!Schema::hasColumn('staff', 'present_address')) {
                $table->text('present_address')->nullable()->after('email');
            }
            if (!Schema::hasColumn('staff', 'permanent_address')) {
                $table->text('permanent_address')->nullable()->after('present_address');
            }

            // Education and Experience as JSON
            if (!Schema::hasColumn('staff', 'education')) {
                $table->json('education')->nullable()->after('permanent_address');
            }
            if (!Schema::hasColumn('staff', 'experience')) {
                $table->json('experience')->nullable()->after('education');
            }

            // Department relationship
            if (!Schema::hasColumn('staff', 'department_id')) {
                $table->foreignId('department_id')->nullable()->after('designation_id')
                    ->constrained('departments')->nullOnDelete();
            }

            // Photo field for FileUpload (direct file, not media library)
            if (!Schema::hasColumn('staff', 'photo')) {
                $table->string('photo')->nullable()->after('notes');
            }

            // Documents field for multiple files
            if (!Schema::hasColumn('staff', 'documents')) {
                $table->json('documents')->nullable()->after('photo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            if (Schema::hasColumn('staff', 'mother_name'))
                $table->dropColumn('mother_name');
            if (Schema::hasColumn('staff', 'emergency_phone'))
                $table->dropColumn('emergency_phone');
            if (Schema::hasColumn('staff', 'present_address'))
                $table->dropColumn('present_address');
            if (Schema::hasColumn('staff', 'permanent_address'))
                $table->dropColumn('permanent_address');
            if (Schema::hasColumn('staff', 'education'))
                $table->dropColumn('education');
            if (Schema::hasColumn('staff', 'experience'))
                $table->dropColumn('experience');
            if (Schema::hasColumn('staff', 'photo'))
                $table->dropColumn('photo');
            if (Schema::hasColumn('staff', 'documents'))
                $table->dropColumn('documents');

            if (Schema::hasColumn('staff', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }
        });
    }
};
