<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassName;
use App\Models\FeePayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeStudents = Student::where('status', 'active')->count();
        $activeTeachers = Teacher::where('status', 'active')->count();
        $totalClasses = ClassName::where('is_active', true)->count();

        // This month's collection (placeholder - will work after fee_payments table exists)
        $monthlyCollection = 0;
        try {
            $monthlyCollection = FeePayment::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount');
        } catch (\Exception $e) {
            // Table doesn't exist yet
        }

        return [
            Stat::make('মোট ছাত্র', $activeStudents)
                ->description('সক্রিয় ছাত্র সংখ্যা')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),

            Stat::make('মোট শিক্ষক', $activeTeachers)
                ->description('সক্রিয় শিক্ষক সংখ্যা')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->chart([3, 2, 4, 3, 4, 3, 4, 5]),

            Stat::make('শ্রেণি সংখ্যা', $totalClasses)
                ->description('সক্রিয় শ্রেণি')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('warning'),

            Stat::make('এই মাসের আয়', '৳' . number_format($monthlyCollection))
                ->description('বেতন ও ফি থেকে')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([5, 8, 6, 9, 7, 8, 10, 12]),
        ];
    }
}
