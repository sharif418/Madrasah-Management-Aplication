<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Meal Menu Table
        Schema::create('meal_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->nullable()->constrained('hostels')->nullOnDelete();
            $table->string('day_of_week'); // saturday, sunday, etc.
            $table->string('meal_type'); // breakfast, lunch, dinner, snacks
            $table->json('menu_items'); // Array of food items
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['hostel_id', 'day_of_week', 'meal_type']);
        });

        // Hostel Visitor Log Table
        Schema::create('hostel_visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained('hostels')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('visitor_name');
            $table->string('visitor_phone')->nullable();
            $table->string('visitor_nid')->nullable();
            $table->string('relation'); // father, mother, brother, etc.
            $table->text('purpose')->nullable();
            $table->datetime('check_in');
            $table->datetime('check_out')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['hostel_id', 'check_in']);
            $table->index(['student_id', 'check_in']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_visitors');
        Schema::dropIfExists('meal_menus');
    }
};
