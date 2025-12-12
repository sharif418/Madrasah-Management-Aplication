<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Book Categories (বই ক্যাটাগরি)
        Schema::create('book_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // কুরআন, হাদিস, ফিকহ, ইতিহাস
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Books (বই)
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // বই এর নাম
            $table->string('title_en')->nullable();
            $table->foreignId('category_id')->constrained('book_categories')->cascadeOnDelete();
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->string('isbn')->nullable();
            $table->year('publish_year')->nullable();
            $table->string('edition')->nullable();
            $table->string('language')->default('বাংলা');
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->string('shelf_location')->nullable(); // তাকের অবস্থান
            $table->decimal('price', 10, 2)->nullable();
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // Library Members (লাইব্রেরি সদস্য)
        Schema::create('library_members', function (Blueprint $table) {
            $table->id();
            $table->string('member_id')->unique();
            $table->enum('member_type', ['student', 'teacher', 'staff', 'external']);
            $table->unsignedBigInteger('reference_id')->nullable(); // student_id, teacher_id etc
            $table->string('name');
            $table->string('phone')->nullable();
            $table->integer('max_books')->default(3); // সর্বোচ্চ কতটি বই নিতে পারবে
            $table->date('membership_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['active', 'expired', 'suspended'])->default('active');
            $table->timestamps();
        });

        // Book Issues (বই ইস্যু)
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('library_member_id')->constrained()->cascadeOnDelete();
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->boolean('fine_paid')->default(false);
            $table->enum('status', ['issued', 'returned', 'overdue', 'lost'])->default('issued');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Hostels (হোস্টেল)
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ছাত্র হোস্টেল-১
            $table->enum('type', ['boys', 'girls', 'mixed'])->default('boys');
            $table->text('address')->nullable();
            $table->string('warden_name')->nullable();
            $table->string('warden_phone')->nullable();
            $table->integer('total_rooms')->default(0);
            $table->integer('total_beds')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Hostel Rooms (হোস্টেল রুম)
        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained()->cascadeOnDelete();
            $table->string('room_no');
            $table->enum('type', ['single', 'double', 'triple', 'dormitory'])->default('double');
            $table->integer('capacity');
            $table->decimal('monthly_rent', 10, 2)->default(0);
            $table->integer('floor')->nullable();
            $table->text('facilities')->nullable();
            $table->enum('status', ['available', 'full', 'maintenance'])->default('available');
            $table->timestamps();

            $table->unique(['hostel_id', 'room_no']);
        });

        // Hostel Allocations (হোস্টেল বরাদ্দ)
        Schema::create('hostel_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hostel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hostel_room_id')->constrained()->cascadeOnDelete();
            $table->string('bed_no')->nullable();
            $table->date('allocation_date');
            $table->date('vacate_date')->nullable();
            $table->enum('status', ['active', 'vacated'])->default('active');
            $table->timestamps();
        });

        // Vehicles (যানবাহন)
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_no'); // গাড়ি নম্বর
            $table->string('vehicle_type'); // বাস, মাইক্রোবাস, ভ্যান
            $table->integer('capacity');
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('driver_license')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Transport Routes (পরিবহন রুট)
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // রুট-১ (মিরপুর)
            $table->text('stops')->nullable(); // JSON - স্টপেজের তালিকা
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Transport Allocations (পরিবহন বরাদ্দ)
        Schema::create('transport_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transport_route_id')->constrained()->cascadeOnDelete();
            $table->string('pickup_point')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_allocations');
        Schema::dropIfExists('transport_routes');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('hostel_allocations');
        Schema::dropIfExists('hostel_rooms');
        Schema::dropIfExists('hostels');
        Schema::dropIfExists('book_issues');
        Schema::dropIfExists('library_members');
        Schema::dropIfExists('books');
        Schema::dropIfExists('book_categories');
    }
};
