<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassName;
use App\Models\Section;
use App\Models\Student;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use App\Filament\Pages\BasePage;
use Illuminate\Support\Carbon;

class AttendanceCalendar extends BasePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'ক্যালেন্ডার ভিউ';

    protected static ?string $title = 'উপস্থিতি ক্যালেন্ডার';

    protected static ?string $navigationGroup = 'উপস্থিতি ব্যবস্থাপনা';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.pages.attendance-calendar';

    public ?array $data = [];
    public ?array $calendarData = null;
    public int $currentMonth;
    public int $currentYear;

    public function mount(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;

        $this->form->fill([
            'month' => $this->currentMonth,
            'year' => $this->currentYear,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('class_id')
                    ->label('শ্রেণি')
                    ->options(ClassName::where('is_active', true)->orderBy('order')->pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function () {
                        $this->data['section_id'] = null;
                        $this->data['student_id'] = null;
                    }),

                Select::make('section_id')
                    ->label('শাখা')
                    ->options(fn(Get $get) => Section::where('class_id', $get('class_id'))->pluck('name', 'id'))
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(fn() => $this->data['student_id'] = null),

                Select::make('student_id')
                    ->label('ছাত্র')
                    ->options(function (Get $get) {
                        $classId = $get('class_id');
                        $sectionId = $get('section_id');
                        if (!$classId)
                            return [];

                        $query = Student::where('class_id', $classId)->where('status', 'active');
                        if ($sectionId) {
                            $query->where('section_id', $sectionId);
                        }
                        return $query->pluck('name', 'id');
                    })
                    ->searchable()
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

    public function loadCalendar(): void
    {
        $this->form->validate();

        $classId = $this->data['class_id'];
        $sectionId = $this->data['section_id'] ?? null;
        $studentId = $this->data['student_id'] ?? null;
        $month = (int) $this->data['month'];
        $year = (int) $this->data['year'];

        $this->currentMonth = $month;
        $this->currentYear = $year;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;
        $firstDayOfWeek = $startDate->dayOfWeek; // 0 = Sunday, 5 = Friday, 6 = Saturday

        // Get attendance data
        $query = Attendance::where('class_id', $classId)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }
        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        $attendanceRecords = $query->get();

        // Group by date
        $attendanceByDate = $attendanceRecords->groupBy(fn($a) => Carbon::parse($a->date)->format('Y-m-d'));

        // Build calendar grid
        $calendar = [];
        $weekDays = ['শনি', 'রবি', 'সোম', 'মঙ্গল', 'বুধ', 'বৃহ', 'শুক্র'];

        // Calculate starting position (Saturday = 0 in our calendar)
        $startPos = ($startDate->dayOfWeek + 1) % 7; // Convert to Saturday-based week

        $currentWeek = array_fill(0, 7, null);
        $dayCounter = 1;

        // Fill empty cells before first day
        for ($i = 0; $i < $startPos; $i++) {
            $currentWeek[$i] = null;
        }

        // Fill calendar days
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            $dateStr = $date->format('Y-m-d');
            $pos = ($startPos + $day - 1) % 7;

            $dayData = [
                'day' => $day,
                'date' => $dateStr,
                'is_friday' => $date->isFriday(),
                'is_today' => $date->isToday(),
                'attendance' => null,
                'summary' => null,
            ];

            if (isset($attendanceByDate[$dateStr])) {
                $dayAttendance = $attendanceByDate[$dateStr];

                if ($studentId) {
                    // Single student view
                    $record = $dayAttendance->first();
                    $dayData['attendance'] = $record ? $record->status : null;
                    $dayData['in_time'] = $record?->in_time;
                } else {
                    // Class summary view
                    $present = $dayAttendance->whereIn('status', ['present', 'late'])->count();
                    $absent = $dayAttendance->where('status', 'absent')->count();
                    $total = $dayAttendance->count();

                    $dayData['summary'] = [
                        'present' => $present,
                        'absent' => $absent,
                        'total' => $total,
                        'percentage' => $total > 0 ? round(($present / $total) * 100) : 0,
                    ];
                }
            }

            $currentWeek[$pos] = $dayData;

            // If end of week (Friday) or end of month, add week to calendar
            if ($pos == 6 || $day == $daysInMonth) {
                $calendar[] = $currentWeek;
                $currentWeek = array_fill(0, 7, null);
            }
        }

        // Get summary stats
        $totalDays = $attendanceByDate->count();
        $presentDays = 0;
        $absentDays = 0;
        $lateDays = 0;

        if ($studentId) {
            foreach ($attendanceByDate as $dayRecords) {
                $record = $dayRecords->first();
                if ($record) {
                    if ($record->status === 'present')
                        $presentDays++;
                    elseif ($record->status === 'absent')
                        $absentDays++;
                    elseif ($record->status === 'late')
                        $lateDays++;
                }
            }
        }

        $this->calendarData = [
            'weeks' => $calendar,
            'weekDays' => $weekDays,
            'month' => $month,
            'year' => $year,
            'month_name' => $this->getBengaliMonth($month),
            'is_student_view' => (bool) $studentId,
            'student' => $studentId ? Student::find($studentId) : null,
            'class' => ClassName::find($classId),
            'summary' => [
                'total_days' => $totalDays,
                'present' => $presentDays,
                'absent' => $absentDays,
                'late' => $lateDays,
                'percentage' => $totalDays > 0 ? round((($presentDays + $lateDays) / $totalDays) * 100, 1) : 0,
            ],
        ];

        Notification::make()->title('ক্যালেন্ডার লোড হয়েছে')->success()->send();
    }

    public function previousMonth(): void
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->data['month'] = $date->month;
        $this->data['year'] = $date->year;
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;

        if (isset($this->data['class_id'])) {
            $this->loadCalendar();
        }
    }

    public function nextMonth(): void
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->data['month'] = $date->month;
        $this->data['year'] = $date->year;
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;

        if (isset($this->data['class_id'])) {
            $this->loadCalendar();
        }
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
}
