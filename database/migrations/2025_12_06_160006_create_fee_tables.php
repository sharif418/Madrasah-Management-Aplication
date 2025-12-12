<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Fee Types (ফি এর ধরণ)
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ভর্তি ফি, মাসিক বেতন, পরীক্ষা ফি
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_recurring')->default(false); // মাসিক কি না
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Fee Structures (ফি স্ট্রাকচার)
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_type_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->decimal('late_fee', 10, 2)->default(0); // দেরিতে দিলে extra
            $table->integer('due_day')->nullable(); // মাসের কত তারিখের মধ্যে দিতে হবে
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['academic_year_id', 'class_id', 'fee_type_id'], 'fee_structure_unique');
        });

        // Fee Discounts (ফি ছাড়)
        Schema::create('fee_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // এতিম, গরীব, মেধাবী, ভাই-বোন
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('amount', 10, 2); // percentage or fixed amount
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Student Fee Assignments (ছাত্রের ফি নির্ধারণ)
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_structure_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_discount_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('month')->nullable(); // 1-12 for monthly fees
            $table->integer('year');
            $table->decimal('original_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'partial', 'paid', 'waived'])->default('pending');
            $table->timestamps();

            $table->index(['student_id', 'year', 'month']);
        });

        // Fee Payments (ফি পরিশোধ)
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no')->unique(); // রসিদ নম্বর
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_fee_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'bkash', 'nagad', 'rocket', 'bank', 'check', 'online'])->default('cash');
            $table->string('transaction_id')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Fee Waivers (ফি মওকুফ)
        Schema::create('fee_waivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_fee_id')->constrained()->cascadeOnDelete();
            $table->decimal('waiver_amount', 10, 2);
            $table->text('reason');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_waivers');
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('student_fees');
        Schema::dropIfExists('fee_discounts');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_types');
    }
};
