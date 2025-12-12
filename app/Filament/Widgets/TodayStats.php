<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\Event;
use App\Models\Notice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TodayStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $today = now()->toDateString();

        // Today's attendance
        $presentToday = 0;
        $absentToday = 0;
        try {
            $presentToday = Attendance::whereDate('date', $today)->where('status', 'present')->count();
            $absentToday = Attendance::whereDate('date', $today)->where('status', 'absent')->count();
        } catch (\Exception $e) {
        }

        // Pending leaves
        $pendingLeaves = 0;
        try {
            $pendingLeaves = LeaveApplication::where('status', 'pending')->count();
        } catch (\Exception $e) {
        }

        // Upcoming events
        $upcomingEvents = 0;
        try {
            $upcomingEvents = Event::where('start_date', '>=', $today)
                ->where('start_date', '<=', now()->addDays(7)->toDateString())
                ->count();
        } catch (\Exception $e) {
        }

        // Active notices
        $activeNotices = 0;
        try {
            $activeNotices = Notice::where('is_published', true)
                ->where('publish_date', '<=', $today)
                ->where(function ($q) use ($today) {
                    $q->whereNull('expiry_date')
                        ->orWhere('expiry_date', '>=', $today);
                })
                ->count();
        } catch (\Exception $e) {
        }

        return [
            Stat::make('আজকে উপস্থিত', $presentToday)
                ->description('ছাত্র')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('আজকে অনুপস্থিত', $absentToday)
                ->description('ছাত্র')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('পেন্ডিং ছুটি', $pendingLeaves)
                ->description('আবেদন')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('আসন্ন ইভেন্ট', $upcomingEvents)
                ->description('৭ দিনের মধ্যে')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
