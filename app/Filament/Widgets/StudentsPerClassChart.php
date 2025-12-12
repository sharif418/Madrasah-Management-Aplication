<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\ClassName;
use Filament\Widgets\ChartWidget;

class StudentsPerClassChart extends ChartWidget
{
    protected static ?string $heading = 'শ্রেণি অনুযায়ী ছাত্র সংখ্যা';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $classes = ClassName::withCount([
            'students' => function ($query) {
                $query->where('status', 'active');
            }
        ])
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'ছাত্র সংখ্যা',
                    'data' => $classes->pluck('students_count')->toArray(),
                    'backgroundColor' => [
                        '#10B981',
                        '#3B82F6',
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6',
                        '#EC4899',
                        '#06B6D4',
                        '#84CC16',
                        '#F97316',
                        '#6366F1',
                    ],
                ],
            ],
            'labels' => $classes->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
