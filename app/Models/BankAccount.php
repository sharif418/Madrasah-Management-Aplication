<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    protected $fillable = [
        'name',
        'bank_name',
        'account_number',
        'branch',
        'opening_balance',
        'current_balance',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Update balance after income
     */
    public function addIncome(float $amount): void
    {
        $this->current_balance = (float) $this->current_balance + $amount;
        $this->save();
    }

    /**
     * Update balance after expense
     */
    public function deductExpense(float $amount): void
    {
        $this->current_balance = (float) $this->current_balance - $amount;
        $this->save();
    }
}
