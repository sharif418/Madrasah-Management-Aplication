<?php

namespace App\Filament\Parent\Pages;

use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\FeePayment;
use App\Models\Student;
use App\Models\StudentFee;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'ড্যাশবোর্ড';

    protected static ?string $title = 'অভিভাবক ড্যাশবোর্ড';

    protected static string $view = 'filament.parent.pages.dashboard';

    protected static ?int $navigationSort = 1;

    public function getChildren()
    {
        $guardian = Auth::user()->guardian;

        if (!$guardian) {
            return collect();
        }

        return Student::with(['class', 'section', 'academicYear'])
            ->where('guardian_id', $guardian->id)
            ->where('status', 'active')
            ->get();
    }

    public function getStats(): array
    {
        $guardian = Auth::user()->guardian;

        if (!$guardian) {
            return [
                'total_children' => 0,
                'total_due' => 0,
                'attendance_percent' => 0,
                'latest_result' => null,
            ];
        }

        $studentIds = Student::where('guardian_id', $guardian->id)
            ->where('status', 'active')
            ->pluck('id');

        // Total due fees
        $totalDue = StudentFee::whereIn('student_id', $studentIds)
            ->where('status', '!=', 'paid')
            ->sum('amount');

        $totalPaid = FeePayment::whereIn('student_id', $studentIds)->sum('amount');

        // This month attendance
        $thisMonth = now()->startOfMonth();
        $attendanceRecords = Attendance::whereIn('student_id', $studentIds)
            ->where('date', '>=', $thisMonth)
            ->get();

        $totalDays = $attendanceRecords->unique('date')->count();
        $presentDays = $attendanceRecords->whereIn('status', ['present', 'late'])->count();
        $attendancePercent = $totalDays > 0 ? round(($presentDays / ($totalDays * $studentIds->count())) * 100, 1) : 0;

        // Latest result
        $latestResult = ExamResult::with(['exam', 'student'])
            ->whereIn('student_id', $studentIds)
            ->orderBy('created_at', 'desc')
            ->first();

        return [
            'total_children' => $studentIds->count(),
            'total_due' => $totalDue - $totalPaid,
            'attendance_percent' => min($attendancePercent, 100),
            'latest_result' => $latestResult,
        ];
    }
}
