<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Fee Installments (কিস্তি)
        Schema::create('fee_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_fee_id')->constrained()->cascadeOnDelete();
            $table->integer('installment_no'); // 1, 2, 3...
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('paid_date')->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue', 'partial'])->default('pending');
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['student_fee_id', 'installment_no']);
        });

        // Fee Refunds (ফি ফেরত)
        Schema::create('fee_refunds', function (Blueprint $table) {
            $table->id();
            $table->string('refund_no')->unique(); // RF-2024-001
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_fee_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('refund_amount', 10, 2);
            $table->text('reason');
            $table->enum('refund_method', ['cash', 'bkash', 'nagad', 'bank'])->default('cash');
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending', 'approved', 'completed', 'rejected'])->default('pending');
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('refund_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // Add installment columns to student_fees if not exists
        if (!Schema::hasColumn('student_fees', 'is_installment')) {
            Schema::table('student_fees', function (Blueprint $table) {
                $table->boolean('is_installment')->default(false)->after('status');
                $table->integer('total_installments')->nullable()->after('is_installment');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_refunds');
        Schema::dropIfExists('fee_installments');

        if (Schema::hasColumn('student_fees', 'is_installment')) {
            Schema::table('student_fees', function (Blueprint $table) {
                $table->dropColumn(['is_installment', 'total_installments']);
            });
        }
    }
};
