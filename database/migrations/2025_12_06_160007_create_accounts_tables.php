<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Income Heads (আয়ের খাত)
        Schema::create('income_heads', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ছাত্র বেতন, দান, সরকারি অনুদান
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Expense Heads (ব্যয়ের খাত)
        Schema::create('expense_heads', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // বেতন, বিদ্যুৎ বিল, রক্ষণাবেক্ষণ
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Bank Accounts (ব্যাংক একাউন্ট)
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // চলতি হিসাব, সঞ্চয়ী হিসাব
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('branch')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Incomes (আয়)
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('income_head_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->enum('payment_method', ['cash', 'bank', 'bkash', 'nagad', 'rocket', 'check'])->default('cash');
            $table->foreignId('bank_account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference_no')->nullable();
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Expenses (ব্যয়)
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_head_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('vendor')->nullable(); // কার কাছ থেকে কেনা
            $table->enum('payment_method', ['cash', 'bank', 'bkash', 'nagad', 'rocket', 'check'])->default('cash');
            $table->foreignId('bank_account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Salary Structures (বেতন কাঠামো)
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('designation_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('house_allowance', 10, 2)->default(0);
            $table->decimal('medical_allowance', 10, 2)->default(0);
            $table->decimal('transport_allowance', 10, 2)->default(0);
            $table->decimal('other_allowance', 10, 2)->default(0);
            $table->decimal('total_salary', 10, 2)->default(0);
            $table->timestamps();
        });

        // Salary Payments (বেতন পরিশোধ)
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->enum('employee_type', ['teacher', 'staff']);
            $table->unsignedBigInteger('employee_id');
            $table->integer('month'); // 1-12
            $table->integer('year');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('allowances', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('advance_deduction', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->enum('payment_method', ['cash', 'bank', 'bkash'])->default('cash');
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['employee_type', 'employee_id', 'month', 'year'], 'salary_unique');
        });

        // Advance Salaries (অগ্রিম বেতন)
        Schema::create('advance_salaries', function (Blueprint $table) {
            $table->id();
            $table->enum('employee_type', ['teacher', 'staff']);
            $table->unsignedBigInteger('employee_id');
            $table->decimal('amount', 10, 2);
            $table->date('given_date');
            $table->integer('installments')->default(1); // কত কিস্তিতে শোধ
            $table->decimal('monthly_deduction', 10, 2);
            $table->decimal('remaining_amount', 10, 2);
            $table->enum('status', ['active', 'completed'])->default('active');
            $table->text('reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Donations/Funds (দান-অনুদান)
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('donor_name');
            $table->string('donor_phone')->nullable();
            $table->string('donor_email')->nullable();
            $table->text('donor_address')->nullable();
            $table->enum('fund_type', ['general', 'zakat', 'sadaqah', 'lillah', 'building', 'scholarship', 'other'])->default('general');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->enum('payment_method', ['cash', 'bank', 'bkash', 'nagad', 'check'])->default('cash');
            $table->string('transaction_id')->nullable();
            $table->text('purpose')->nullable(); // কি উদ্দেশ্যে দান
            $table->string('receipt_no')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
        Schema::dropIfExists('advance_salaries');
        Schema::dropIfExists('salary_payments');
        Schema::dropIfExists('salary_structures');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('expense_heads');
        Schema::dropIfExists('income_heads');
    }
};
