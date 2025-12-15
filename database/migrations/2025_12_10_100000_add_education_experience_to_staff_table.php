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
            // Check if reference columns exist before using 'after', otherwise just add at end

            // New personal fields
            if (!Schema::hasColumn('staff', 'mother_name')) {
                if (Schema::hasColumn('staff', 'father_name')) {
                    $table->string('mother_name')->nullable()->after('father_name');
                } else {
                    $table->string('mother_name')->nullable();
                }
            }

            if (!Schema::hasColumn('staff', 'emergency_phone')) {
                if (Schema::hasColumn('staff', 'phone')) {
                    $table->string('emergency_phone')->nullable()->after('phone');
                } else {
                    $table->string('emergency_phone')->nullable();
                }
            }

            // Replace single address with present/permanent
            if (!Schema::hasColumn('staff', 'present_address')) {
                if (Schema::hasColumn('staff', 'email')) {
                    $table->text('present_address')->nullable()->after('email');
                } else {
                    $table->text('present_address')->nullable();
                }
            }
            if (!Schema::hasColumn('staff', 'permanent_address')) {
                $table->text('permanent_address')->nullable(); // Just add, order doesn't matter much if prev col unstable
            }

            // Education and Experience as JSON
            if (!Schema::hasColumn('staff', 'education')) {
                $table->json('education')->nullable();
            }
            if (!Schema::hasColumn('staff', 'experience')) {
                $table->json('experience')->nullable();
            }

            // Department relationship
            if (!Schema::hasColumn('staff', 'department_id')) {
                if (Schema::hasColumn('staff', 'designation_id')) {
                    $table->foreignId('department_id')->nullable()->after('designation_id')
                        ->constrained('departments')->nullOnDelete();
                } else {
                    $table->foreignId('department_id')->nullable()
                        ->constrained('departments')->nullOnDelete();
                }
            }

            // Photo field 
            if (!Schema::hasColumn('staff', 'photo')) {
                if (Schema::hasColumn('staff', 'notes')) {
                    $table->string('photo')->nullable()->after('notes');
                } else {
                    $table->string('photo')->nullable();
                }
            }

            // Documents field
            if (!Schema::hasColumn('staff', 'documents')) {
                $table->json('documents')->nullable();
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
