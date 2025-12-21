<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassName;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use App\Filament\Pages\BasePage;
use Illuminate\Support\Facades\DB;

class AttendanceReport extends BasePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'হাজিরা রিপোর্ট';

    protected static ?string $title = 'হাজিরা রিপোর্ট (Attendance Report)';

    protected static ?string $navigationGroup = 'রিপোর্ট';

    protected static string $view = 'filament.pages.attendance-report';

    public ?array $data = [];
    public ?array $reportData = null;

    public function mount(): void
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $this->form->fill([
            'academic_year_id' => $currentYear?->id,
            'start_date' => now()->startOfMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('academic_year_id')
                    ->label('শিক্ষাবর্ষ')
                    ->options(AcademicYear::pluck('name', 'id'))
                    ->required(),
                Select::make('class_id')
                    ->label('ক্লাস')
                    ->options(ClassName::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn() => $this->data['section_id'] = null),
                Select::make('section_id')
                    ->label('শাখা')
                    ->options(fn(Get $get) => Section::where('class_id', $get('class_id'))->pluck('name', 'id')),
                DatePicker::make('start_date')
                    ->label('শুরুর তারিখ')
                    ->required()
                    ->native(false),
                DatePicker::make('end_date')
                    ->label('শেষের তারিখ')
                    ->required()
                    ->native(false),
            ])
            ->columns(5)
            ->statePath('data');
    }

    public function generate(): void
    {
        $this->form->validate();

        $classId = $this->data['class_id'];
        $sectionId = $this->data['section_id'] ?? null;
        $startDate = $this->data['start_date'];
        $endDate = $this->data['end_date'];

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

        $attendanceData = [];
        $totalDays = 0;

        // Get all attendance dates in range
        $dates = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('class_id', $classId)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->select('date')
            ->distinct()
            ->orderBy('date')
            ->pluck('date')
            ->toArray();

        $totalDays = count($dates);

        foreach ($students as $student) {
            $attendances = Attendance::where('student_id', $student->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->keyBy(fn($a) => $a->date->format('Y-m-d'));

            $present = $attendances->where('status', 'present')->count();
            $absent = $attendances->where('status', 'absent')->count();
            $late = $attendances->where('status', 'late')->count();
            $leave = $attendances->where('status', 'leave')->count();

            $percentage = $totalDays > 0 ? round((($present + $late) / $totalDays) * 100, 1) : 0;

            $attendanceData[] = [
                'student' => $student,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'leave' => $leave,
                'total' => $totalDays,
                'percentage' => $percentage,
            ];
        }

        $className = ClassName::find($classId);
        $sectionName = $sectionId ? Section::find($sectionId)?->name : 'সকল শাখা';

        $this->reportData = [
            'students' => $attendanceData,
            'class' => $className,
            'section' => $sectionName,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'dates' => $dates,
        ];

        Notification::make()->title('রিপোর্ট তৈরি হয়েছে')->success()->send();
    }

    public function downloadPdf()
    {
        if (!$this->reportData) {
            Notification::make()->title('প্রথমে রিপোর্ট তৈরি করুন')->warning()->send();
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

        $pdf = Pdf::loadView('pdf.attendance-report', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'attendance-report-' . now()->timestamp . '.pdf');
    }

    // Summary stats for view
    public function getSummaryStats(): array
    {
        if (!$this->reportData) {
            return [];
        }

        $students = collect($this->reportData['students']);
        $totalStudents = $students->count();
        $avgAttendance = $students->avg('percentage');
        $above90 = $students->where('percentage', '>=', 90)->count();
        $below50 = $students->where('percentage', '<', 50)->count();

        return [
            'total_students' => $totalStudents,
            'avg_attendance' => round($avgAttendance, 1),
            'above_90' => $above90,
            'below_50' => $below50,
        ];
    }
}
