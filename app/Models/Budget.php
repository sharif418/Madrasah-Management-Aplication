<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = [
        'fiscal_year',
        'type',
        'income_head_id',
        'expense_head_id',
        'budgeted_amount',
        'actual_amount',
        'notes',
    ];

    protected $casts = [
        'budgeted_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
    ];

    public function incomeHead(): BelongsTo
    {
        return $this->belongsTo(IncomeHead::class);
    }

    public function expenseHead(): BelongsTo
    {
        return $this->belongsTo(ExpenseHead::class);
    }

    public function getHeadNameAttribute(): string
    {
        if ($this->type === 'income') {
            return $this->incomeHead?->name ?? 'Unknown';
        }
        return $this->expenseHead?->name ?? 'Unknown';
    }

    public function getVarianceAttribute(): float
    {
        return $this->budgeted_amount - $this->actual_amount;
    }

    public function getVariancePercentageAttribute(): float
    {
        if ($this->budgeted_amount <= 0)
            return 0;
        return round(($this->variance / $this->budgeted_amount) * 100, 2);
    }

    public function getIsOverBudgetAttribute(): bool
    {
        return $this->actual_amount > $this->budgeted_amount;
    }

    public static function getFiscalYearOptions(): array
    {
        $currentYear = now()->year;
        $options = [];
        for ($y = $currentYear - 2; $y <= $currentYear + 1; $y++) {
            $fy = $y . '-' . ($y + 1);
            $options[$fy] = $fy;
        }
        return $options;
    }
}
