<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\ClassName;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DailyAttendanceWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $todaySummary = Attendance::getTodaySummary();
        $totalStudents = Student::where('status', 'active')->count();
        $unmarkedClasses = Attendance::getUnmarkedClasses();
        $unmarkedCount = count($unmarkedClasses);

        // Calculate attendance for last 7 days for chart
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayAttendance = Attendance::whereDate('date', $date)
                ->where('status', 'present')
                ->count();
            $chartData[] = $dayAttendance;
        }

        return [
            Stat::make('আজকের উপস্থিতি', $todaySummary['present'] . '/' . $todaySummary['total'])
                ->description($todaySummary['percentage'] . '% উপস্থিত')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($todaySummary['percentage'] >= 80 ? 'success' : ($todaySummary['percentage'] >= 50 ? 'warning' : 'danger'))
                ->chart($chartData),

            Stat::make('অনুপস্থিত', $todaySummary['absent'])
                ->description('আজকের অনুপস্থিত ছাত্র')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($todaySummary['absent'] > 0 ? 'danger' : 'success'),

            Stat::make('দেরিতে আসা', $todaySummary['late'])
                ->description('আজকের বিলম্বে আগত')
                ->descriptionIcon('heroicon-m-clock')
                ->color($todaySummary['late'] > 0 ? 'warning' : 'success'),

            Stat::make('হাজিরা বাকি', $unmarkedCount . ' শ্রেণি')
                ->description($unmarkedCount > 0 ? 'হাজিরা দেওয়া হয়নি' : 'সব শ্রেণির হাজিরা সম্পন্ন')
                ->descriptionIcon($unmarkedCount > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->color($unmarkedCount > 0 ? 'warning' : 'success'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
