<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'expense_head_id',
        'title',
        'amount',
        'date',
        'vendor',
        'payment_method',
        'bank_account_id',
        'reference_no',
        'invoice_no',
        'description',
        'attachment',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public const METHOD_CASH = 'cash';
    public const METHOD_BANK = 'bank';
    public const METHOD_BKASH = 'bkash';
    public const METHOD_NAGAD = 'nagad';
    public const METHOD_ROCKET = 'rocket';
    public const METHOD_CHECK = 'check';

    public static function paymentMethodOptions(): array
    {
        return [
            self::METHOD_CASH => 'নগদ',
            self::METHOD_BANK => 'ব্যাংক',
            self::METHOD_BKASH => 'বিকাশ',
            self::METHOD_NAGAD => 'নগদ (Nagad)',
            self::METHOD_ROCKET => 'রকেট',
            self::METHOD_CHECK => 'চেক',
        ];
    }

    public function expenseHead(): BelongsTo
    {
        return $this->belongsTo(ExpenseHead::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($expense) {
            // Deduct from bank balance if bank account is used
            if ($expense->bank_account_id && $expense->bankAccount) {
                $expense->bankAccount->deductExpense($expense->amount);
            }
        });

        static::deleted(function ($expense) {
            // Reverse bank balance if deleted
            if ($expense->bank_account_id && $expense->bankAccount) {
                $expense->bankAccount->addIncome($expense->amount);
            }
        });
    }
}
