<?php

namespace App\Filament\Widgets;

use App\Models\StudentFee;
use App\Models\FeePayment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class FeeCollectionChart extends ChartWidget
{
    protected static ?string $heading = 'মাসিক ফি আদায়';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Last 6 months collection
        $months = [];
        $collections = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');

            $total = 0;
            try {
                $total = FeePayment::whereMonth('payment_date', $date->month)
                    ->whereYear('payment_date', $date->year)
                    ->sum('total_amount');
            } catch (\Exception $e) {
            }

            $collections[] = $total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'ফি আদায় (টাকা)',
                    'data' => $collections,
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#059669',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
