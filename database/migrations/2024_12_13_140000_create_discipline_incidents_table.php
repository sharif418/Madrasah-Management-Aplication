<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discipline_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();

            // Incident details
            $table->date('incident_date');
            $table->string('incident_type');
            $table->string('severity'); // minor, moderate, serious, severe
            $table->text('description');
            $table->string('location')->nullable();
            $table->text('witnesses')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();

            // Action taken
            $table->string('action_taken')->nullable();
            $table->date('action_date')->nullable();

            // Parent notification
            $table->boolean('parent_notified')->default(false);
            $table->date('parent_notified_date')->nullable();
            $table->date('parent_meeting_date')->nullable();
            $table->text('parent_meeting_notes')->nullable();

            // Follow-up
            $table->boolean('follow_up_required')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->text('follow_up_notes')->nullable();

            // Status and points
            $table->string('status')->default('reported');
            $table->integer('merit_points')->default(0); // Negative for deduction
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'academic_year_id']);
            $table->index(['incident_date', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discipline_incidents');
    }
};
