<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassName;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Mark;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ReportCardGeneration extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'রিপোর্ট কার্ড';

    protected static ?string $title = 'রিপোর্ট কার্ড তৈরি';

    protected static ?string $navigationGroup = 'পরীক্ষা ব্যবস্থাপনা';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.report-card-generation';

    public ?array $data = [];
    public ?array $reportData = null;
    public bool $showPreview = false;

    public function mount(): void
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $this->form->fill([
            'academic_year_id' => $currentYear?->id,
            'include_attendance' => true,
            'include_photo' => true,
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
                    ->native(false)
                    ->live(),

                Select::make('exam_id')
                    ->label('পরীক্ষা')
                    ->options(function (Get $get) {
                        $yearId = $get('academic_year_id');
                        if (!$yearId)
                            return [];
                        return Exam::where('academic_year_id', $yearId)
                            ->where('is_published', true)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(fn() => $this->data['student_id'] = null),

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
                    ->label('শাখা (ঐচ্ছিক)')
                    ->options(fn(Get $get) => Section::where('class_id', $get('class_id'))->pluck('name', 'id'))
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(fn() => $this->data['student_id'] = null),

                Select::make('student_id')
                    ->label('ছাত্র (ব্যক্তিগত কার্ডের জন্য)')
                    ->options(function (Get $get) {
                        $classId = $get('class_id');
                        $sectionId = $get('section_id');
                        if (!$classId)
                            return [];

                        $query = Student::where('class_id', $classId)->where('status', 'active');
                        if ($sectionId) {
                            $query->where('section_id', $sectionId);
                        }
                        return $query->orderBy('roll_no')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->native(false)
                    ->helperText('খালি রাখলে সকল ছাত্রের কার্ড তৈরি হবে'),

                Toggle::make('include_attendance')
                    ->label('উপস্থিতি তথ্য যোগ করুন')
                    ->default(true),

                Toggle::make('include_photo')
                    ->label('ছবি যোগ করুন')
                    ->default(true),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function generate(): void
    {
        $this->form->validate();

        $examId = $this->data['exam_id'];
        $classId = $this->data['class_id'];
        $sectionId = $this->data['section_id'] ?? null;
        $studentId = $this->data['student_id'] ?? null;
        $includeAttendance = $this->data['include_attendance'] ?? true;
        $includePhoto = $this->data['include_photo'] ?? true;

        $exam = Exam::with(['academicYear', 'examType'])->find($examId);

        if (!$exam) {
            Notification::make()->title('পরীক্ষা পাওয়া যায়নি')->danger()->send();
            return;
        }

        // Get students
        $studentsQuery = Student::with(['class', 'section'])
            ->where('class_id', $classId)
            ->where('status', 'active');

        if ($sectionId) {
            $studentsQuery->where('section_id', $sectionId);
        }
        if ($studentId) {
            $studentsQuery->where('id', $studentId);
        }

        $students = $studentsQuery->orderBy('roll_no')->get();

        if ($students->isEmpty()) {
            Notification::make()->title('কোনো ছাত্র পাওয়া যায়নি')->warning()->send();
            return;
        }

        // Get exam results
        $examResults = ExamResult::where('exam_id', $examId)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        // Get marks with subjects
        $marks = Mark::with(['subject', 'examSchedule'])
            ->where('exam_id', $examId)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        // Build report data
        $reportCards = [];

        foreach ($students as $student) {
            $result = $examResults->get($student->id);
            $studentMarks = $marks->get($student->id, collect());

            // Attendance data
            $attendanceData = null;
            if ($includeAttendance) {
                $attendanceData = Attendance::getMonthlyStudentSummary(
                    $student->id,
                    now()->month,
                    now()->year
                );
            }

            // Subject-wise results
            $subjectResults = [];
            foreach ($studentMarks as $mark) {
                $subjectResults[] = [
                    'subject' => $mark->subject?->name ?? 'Unknown',
                    'full_marks' => $mark->examSchedule?->full_marks ?? 100,
                    'pass_marks' => $mark->examSchedule?->pass_marks ?? 33,
                    'obtained' => $mark->total_marks ?? 0,
                    'grade' => $mark->grade?->name ?? $this->calculateGrade($mark->total_marks, $mark->examSchedule?->full_marks ?? 100),
                    'is_passed' => $mark->is_passed ?? ($mark->total_marks >= ($mark->examSchedule?->pass_marks ?? 33)),
                    'is_absent' => $mark->is_absent ?? false,
                ];
            }

            $reportCards[] = [
                'student' => $student,
                'result' => $result,
                'subjects' => $subjectResults,
                'attendance' => $attendanceData,
                'include_photo' => $includePhoto,
            ];
        }

        $className = ClassName::find($classId);
        $sectionName = $sectionId ? Section::find($sectionId)?->name : null;

        $this->reportData = [
            'exam' => $exam,
            'class' => $className,
            'section' => $sectionName,
            'cards' => $reportCards,
            'generated_at' => now(),
        ];

        $this->showPreview = true;

        Notification::make()->title('রিপোর্ট কার্ড তৈরি হয়েছে')->success()->send();
    }

    public function downloadPdf()
    {
        if (!$this->reportData) {
            Notification::make()->title('প্রথমে Generate করুন')->warning()->send();
            return;
        }

        $data = [
            'reportData' => $this->reportData,
            'institute' => [
                'name' => institution_name(),
                'address' => institution_address(),
                'phone' => institution_phone(),
                'email' => institution_email(),
                'logo' => institution_logo(),
            ],
        ];

        $pdf = Pdf::loadView('pdf.report-card', $data)
            ->setPaper('a4', 'portrait');

        $fileName = 'report-card-' . $this->reportData['exam']->name . '-' . now()->timestamp . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }

    protected function calculateGrade(float $marks, float $fullMarks): string
    {
        $percentage = ($marks / $fullMarks) * 100;

        if ($percentage >= 80)
            return 'A+';
        if ($percentage >= 70)
            return 'A';
        if ($percentage >= 60)
            return 'A-';
        if ($percentage >= 50)
            return 'B';
        if ($percentage >= 40)
            return 'C';
        if ($percentage >= 33)
            return 'D';
        return 'F';
    }
}
