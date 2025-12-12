<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassName;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class MonthlyAttendanceSummary extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'মাসিক সামারি';

    protected static ?string $title = 'মাসিক উপস্থিতি সামারি';

    protected static ?string $navigationGroup = 'উপস্থিতি ব্যবস্থাপনা';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.monthly-attendance-summary';

    public ?array $data = [];
    public ?array $reportData = null;
    public ?array $calendarDays = null;

    public function mount(): void
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $this->form->fill([
            'academic_year_id' => $currentYear?->id,
            'month' => now()->month,
            'year' => now()->year,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('academic_year_id')
                    ->label('শিক্ষাবর্ষ')
                    ->options(AcademicYear::pluck('name', 'id'))
                    ->required()
                    ->native(false),

                Select::make('class_id')
                    ->label('শ্রেণি')
                    ->options(ClassName::where('is_active', true)->orderBy('order')->pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(fn() => $this->data['section_id'] = null),

                Select::make('section_id')
                    ->label('শাখা (ঐচ্ছিক)')
                    ->options(fn(Get $get) => Section::where('class_id', $get('class_id'))->pluck('name', 'id'))
                    ->native(false),

                Select::make('month')
                    ->label('মাস')
                    ->options([
                        1 => 'জানুয়ারি',
                        2 => 'ফেব্রুয়ারি',
                        3 => 'মার্চ',
                        4 => 'এপ্রিল',
                        5 => 'মে',
                        6 => 'জুন',
                        7 => 'জুলাই',
                        8 => 'আগস্ট',
                        9 => 'সেপ্টেম্বর',
                        10 => 'অক্টোবর',
                        11 => 'নভেম্বর',
                        12 => 'ডিসেম্বর',
                    ])
                    ->required()
                    ->native(false)
                    ->default(now()->month),

                Select::make('year')
                    ->label('সাল')
                    ->options(array_combine(
                        range(now()->year - 2, now()->year + 1),
                        range(now()->year - 2, now()->year + 1)
                    ))
                    ->required()
                    ->native(false)
                    ->default(now()->year),
            ])
            ->columns(5)
            ->statePath('data');
    }

    public function generate(): void
    {
        $this->form->validate();

        $classId = $this->data['class_id'];
        $sectionId = $this->data['section_id'] ?? null;
        $month = (int) $this->data['month'];
        $year = (int) $this->data['year'];

        // Get students
        $students = Student::with(['class', 'section'])
            ->where('class_id', $classId)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->where('status', 'active')
            ->orderBy('roll_no')
            ->get();

        if ($students->isEmpty()) {
            Notification::make()->title('কোনো ছাত্র পাওয়া যায়নি')->warning()->send();
            $this->reportData = null;
            return;
        }

        // Get all dates in the month
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        // Get all attendance records for the month
        $allAttendance = Attendance::where('class_id', $classId)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('student_id');

        // Get dates when attendance was taken
        $attendanceDates = Attendance::where('class_id', $classId)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->whereBetween('date', [$startDate, $endDate])
            ->distinct()
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        $totalWorkingDays = count($attendanceDates);

        // Build student summary data
        $studentData = [];
        $overallStats = [
            'total_present' => 0,
            'total_absent' => 0,
            'total_late' => 0,
            'total_leave' => 0,
        ];

        foreach ($students as $student) {
            $studentAttendance = $allAttendance->get($student->id, collect());

            $present = $studentAttendance->where('status', 'present')->count();
            $absent = $studentAttendance->where('status', 'absent')->count();
            $late = $studentAttendance->where('status', 'late')->count();
            $leave = $studentAttendance->where('status', 'leave')->count();
            $halfDay = $studentAttendance->where('status', 'half_day')->count();

            $percentage = $totalWorkingDays > 0
                ? round((($present + $late) / $totalWorkingDays) * 100, 1)
                : 0;

            // Daily status for calendar view
            $dailyStatus = [];
            foreach ($attendanceDates as $dateStr) {
                $dayAttendance = $studentAttendance->first(fn($a) => Carbon::parse($a->date)->format('Y-m-d') === $dateStr);
                $dailyStatus[$dateStr] = $dayAttendance ? $dayAttendance->status : null;
            }

            $studentData[] = [
                'student' => $student,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'leave' => $leave,
                'half_day' => $halfDay,
                'percentage' => $percentage,
                'daily' => $dailyStatus,
            ];

            $overallStats['total_present'] += $present;
            $overallStats['total_absent'] += $absent;
            $overallStats['total_late'] += $late;
            $overallStats['total_leave'] += $leave;
        }

        // Sort by roll number
        usort($studentData, fn($a, $b) => ($a['student']->roll_no ?? 0) <=> ($b['student']->roll_no ?? 0));

        // Calculate overall percentage
        $totalRecords = $overallStats['total_present'] + $overallStats['total_absent'] + $overallStats['total_late'] + $overallStats['total_leave'];
        $overallStats['percentage'] = $totalRecords > 0
            ? round((($overallStats['total_present'] + $overallStats['total_late']) / $totalRecords) * 100, 1)
            : 0;

        $className = ClassName::find($classId);
        $sectionName = $sectionId ? Section::find($sectionId)?->name : 'সকল শাখা';

        $this->reportData = [
            'students' => $studentData,
            'class' => $className,
            'section' => $sectionName,
            'month' => $month,
            'year' => $year,
            'month_name' => $this->getBengaliMonth($month),
            'total_working_days' => $totalWorkingDays,
            'dates' => $attendanceDates,
            'days_in_month' => $daysInMonth,
            'overall_stats' => $overallStats,
        ];

        // Build calendar days
        $this->calendarDays = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            $dateStr = $date->format('Y-m-d');
            $this->calendarDays[] = [
                'day' => $day,
                'date' => $dateStr,
                'is_weekend' => $date->isFriday(), // Friday is weekend in Bangladesh
                'has_attendance' => in_array($dateStr, $attendanceDates),
            ];
        }

        Notification::make()->title('সামারি তৈরি হয়েছে')->success()->send();
    }

    public function downloadPdf()
    {
        if (!$this->reportData) {
            Notification::make()->title('প্রথমে সামারি তৈরি করুন')->warning()->send();
            return;
        }

        $data = [
            'reportData' => $this->reportData,
            'institute' => [
                'name' => institution_name(),
                'address' => institution_address(),
            ],
            'generated_at' => now()->format('d M Y, h:i A'),
        ];

        $pdf = Pdf::loadView('pdf.monthly-attendance-summary', $data)
            ->setPaper('a4', 'landscape');

        $fileName = 'monthly-attendance-' . $this->reportData['month'] . '-' . $this->reportData['year'] . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }

    protected function getBengaliMonth(int $month): string
    {
        $months = [
            1 => 'জানুয়ারি',
            2 => 'ফেব্রুয়ারি',
            3 => 'মার্চ',
            4 => 'এপ্রিল',
            5 => 'মে',
            6 => 'জুন',
            7 => 'জুলাই',
            8 => 'আগস্ট',
            9 => 'সেপ্টেম্বর',
            10 => 'অক্টোবর',
            11 => 'নভেম্বর',
            12 => 'ডিসেম্বর',
        ];
        return $months[$month] ?? '';
    }

    public function getAttendanceColor(string $status): string
    {
        return match ($status) {
            'present' => 'bg-green-500',
            'absent' => 'bg-red-500',
            'late' => 'bg-yellow-500',
            'leave' => 'bg-blue-500',
            'half_day' => 'bg-orange-500',
            default => 'bg-gray-300',
        };
    }
}
