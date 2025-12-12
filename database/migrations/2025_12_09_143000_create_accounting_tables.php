<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Advance Salary Table
        Schema::create('salary_advances', function (Blueprint $table) {
            $table->id();
            $table->string('advance_no')->unique(); // ADV-2024-001
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('advance_date');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'paid', 'deducting', 'completed', 'rejected'])->default('pending');
            $table->decimal('deducted_amount', 10, 2)->default(0);
            $table->integer('deduction_months')->default(1); // কত মাসে কাটবে
            $table->decimal('monthly_deduction', 10, 2)->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // Staff Loans Table
        Schema::create('staff_loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_no')->unique(); // LN-2024-001
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->decimal('loan_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->integer('total_installments');
            $table->decimal('monthly_deduction', 10, 2);
            $table->date('loan_date');
            $table->date('start_deduction_date');
            $table->enum('status', ['pending', 'approved', 'active', 'completed', 'cancelled'])->default('pending');
            $table->text('purpose')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        // Budget Table
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('fiscal_year'); // 2024-2025
            $table->enum('type', ['income', 'expense']);
            $table->foreignId('income_head_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('expense_head_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('budgeted_amount', 12, 2);
            $table->decimal('actual_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['fiscal_year', 'type', 'income_head_id', 'expense_head_id'], 'budget_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('staff_loans');
        Schema::dropIfExists('salary_advances');
    }
};
