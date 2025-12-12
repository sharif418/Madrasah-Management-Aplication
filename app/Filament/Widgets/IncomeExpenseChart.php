<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\ChartWidget;

class IncomeExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'আয়-ব্যয় তুলনা';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $months = [];
        $incomes = [];
        $expenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');

            $incomeTotal = 0;
            $expenseTotal = 0;

            try {
                $incomeTotal = Income::whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->sum('amount');
            } catch (\Exception $e) {
            }

            try {
                $expenseTotal = Expense::whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->sum('amount');
            } catch (\Exception $e) {
            }

            $incomes[] = $incomeTotal;
            $expenses[] = $expenseTotal;
        }

        return [
            'datasets' => [
                [
                    'label' => 'আয়',
                    'data' => $incomes,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
                [
                    'label' => 'ব্যয়',
                    'data' => $expenses,
                    'borderColor' => '#EF4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
