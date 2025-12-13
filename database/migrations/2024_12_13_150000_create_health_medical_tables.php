<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Student Health Profile
        Schema::create('student_healths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();

            // Physical measurements
            $table->decimal('height', 5, 2)->nullable(); // in cm
            $table->decimal('weight', 5, 2)->nullable(); // in kg
            $table->string('blood_group')->nullable();
            $table->string('vision_left')->nullable();
            $table->string('vision_right')->nullable();
            $table->string('hearing_status')->default('normal');

            // Medical conditions
            $table->json('allergies')->nullable();
            $table->json('chronic_conditions')->nullable();
            $table->text('disabilities')->nullable();
            $table->json('current_medications')->nullable();
            $table->json('past_surgeries')->nullable();
            $table->text('family_medical_history')->nullable();
            $table->json('immunization_records')->nullable();

            // Medical contacts
            $table->date('last_physical_exam')->nullable();
            $table->string('doctor_name')->nullable();
            $table->string('doctor_phone')->nullable();
            $table->string('emergency_hospital')->nullable();
            $table->text('insurance_info')->nullable();

            // Other
            $table->text('special_dietary_needs')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });

        // Medical Visits (Sick Room)
        Schema::create('medical_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->datetime('visit_date');
            $table->string('visit_type'); // regular_checkup, sick_visit, injury, emergency
            $table->text('symptoms')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->json('medicines_given')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->string('referred_to')->nullable();

            // Parent notification
            $table->boolean('parent_informed')->default(false);
            $table->datetime('parent_informed_date')->nullable();

            // Follow-up
            $table->boolean('follow_up_required')->default(false);
            $table->date('follow_up_date')->nullable();

            // Metadata
            $table->foreignId('attended_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('treated');

            $table->timestamps();

            $table->index(['student_id', 'visit_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_visits');
        Schema::dropIfExists('student_healths');
    }
};
