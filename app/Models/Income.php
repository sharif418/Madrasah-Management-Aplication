<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    protected $fillable = [
        'income_head_id',
        'title',
        'amount',
        'date',
        'payment_method',
        'bank_account_id',
        'reference_no',
        'description',
        'attachment',
        'created_by',
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

    public function incomeHead(): BelongsTo
    {
        return $this->belongsTo(IncomeHead::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($income) {
            // Update bank balance if bank account is used
            if ($income->bank_account_id && $income->bankAccount) {
                $income->bankAccount->addIncome($income->amount);
            }
        });

        static::deleted(function ($income) {
            // Reverse bank balance if deleted
            if ($income->bank_account_id && $income->bankAccount) {
                $income->bankAccount->deductExpense($income->amount);
            }
        });
    }
}
