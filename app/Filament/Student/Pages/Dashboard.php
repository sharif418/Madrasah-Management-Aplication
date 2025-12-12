<?php

namespace App\Filament\Student\Pages;

use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\FeePayment;
use App\Models\StudentFee;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.student.pages.dashboard';

    protected static ?string $title = 'ড্যাশবোর্ড';

    protected static ?string $navigationLabel = 'ড্যাশবোর্ড';

    protected static ?int $navigationSort = 1;

    public function getStudent()
    {
        return Auth::user()->student;
    }

    public function getAttendanceStats(): array
    {
        $student = $this->getStudent();
        if (!$student) {
            return ['present' => 0, 'absent' => 0, 'late' => 0, 'percentage' => 0];
        }

        $total = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->count();

        $present = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->where('status', 'present')
            ->count();

        $absent = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->where('status', 'absent')
            ->count();

        $late = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->where('status', 'late')
            ->count();

        return [
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
        ];
    }

    public function getFeeStatus(): array
    {
        $student = $this->getStudent();
        if (!$student) {
            return ['total' => 0, 'paid' => 0, 'due' => 0];
        }

        $total = StudentFee::where('student_id', $student->id)->sum('amount');
        $paid = FeePayment::where('student_id', $student->id)->sum('amount');
        $due = $total - $paid;

        return [
            'total' => $total,
            'paid' => $paid,
            'due' => max(0, $due),
        ];
    }

    public function getRecentResults()
    {
        $student = $this->getStudent();
        if (!$student) {
            return collect();
        }

        return ExamResult::with(['exam', 'student'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function getRecentAttendance()
    {
        $student = $this->getStudent();
        if (!$student) {
            return collect();
        }

        return Attendance::where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();
    }
}
