<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\ClassName;
use App\Models\Exam;
use App\Models\ExamResult;
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

class ProgressReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $navigationLabel = 'অগ্রগতি প্রতিবেদন';

    protected static ?string $title = 'বার্ষিক অগ্রগতি প্রতিবেদন';

    protected static ?string $navigationGroup = 'পরীক্ষা ব্যবস্থাপনা';

    protected static ?int $navigationSort = 13;

    protected static string $view = 'filament.pages.progress-report';

    public ?array $data = [];
    public ?array $progressData = null;
    public bool $showReport = false;

    public function mount(): void
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $this->form->fill([
            'academic_year_id' => $currentYear?->id,
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

                Select::make('class_id')
                    ->label('শ্রেণি')
                    ->options(ClassName::where('is_active', true)->orderBy('order')->pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->live(),

                Select::make('section_id')
                    ->label('শাখা (ঐচ্ছিক)')
                    ->options(fn(Get $get) => Section::where('class_id', $get('class_id'))->pluck('name', 'id'))
                    ->native(false)
                    ->live(),

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
                        return $query->orderBy('roll_no')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->native(false),
            ])
            ->columns(4)
            ->statePath('data');
    }

    public function generateReport(): void
    {
        $this->form->validate();

        $yearId = $this->data['academic_year_id'];
        $studentId = $this->data['student_id'];

        $student = Student::with(['class', 'section'])->find($studentId);
        $academicYear = AcademicYear::find($yearId);

        if (!$student) {
            Notification::make()->title('ছাত্র পাওয়া যায়নি')->warning()->send();
            return;
        }

        // Get all exams for this year
        $exams = Exam::where('academic_year_id', $yearId)
            ->where('is_published', true)
            ->orderBy('start_date')
            ->get();

        if ($exams->isEmpty()) {
            Notification::make()->title('এই শিক্ষাবর্ষে কোনো প্রকাশিত পরীক্ষা নেই')->warning()->send();
            return;
        }

        // Get results for each exam
        $examResults = [];
        foreach ($exams as $exam) {
            $result = ExamResult::where('exam_id', $exam->id)
                ->where('student_id', $studentId)
                ->first();

            $examResults[] = [
                'exam' => $exam,
                'result' => $result,
                'total_marks' => $result?->total_marks ?? 0,
                'full_marks' => $result?->total_full_marks ?? 0,
                'percentage' => $result?->percentage ?? 0,
                'gpa' => $result?->gpa ?? 0,
                'grade' => $result?->grade ?? '-',
                'position' => $result?->position ?? '-',
                'result_status' => $result?->result_status ?? 'N/A',
            ];
        }

        // Calculate trends
        $gpaValues = collect($examResults)->pluck('gpa')->filter()->values();
        $percentageValues = collect($examResults)->pluck('percentage')->filter()->values();

        $trend = 'stable';
        if ($gpaValues->count() >= 2) {
            $lastTwo = $gpaValues->slice(-2)->values();
            if ($lastTwo->count() === 2) {
                if ($lastTwo[1] > $lastTwo[0])
                    $trend = 'improving';
                elseif ($lastTwo[1] < $lastTwo[0])
                    $trend = 'declining';
            }
        }

        $this->progressData = [
            'student' => $student,
            'academic_year' => $academicYear,
            'exams' => $examResults,
            'summary' => [
                'total_exams' => count($examResults),
                'passed_count' => collect($examResults)->where('result_status', 'passed')->count(),
                'avg_gpa' => round($gpaValues->avg() ?? 0, 2),
                'avg_percentage' => round($percentageValues->avg() ?? 0, 1),
                'highest_gpa' => $gpaValues->max() ?? 0,
                'trend' => $trend,
            ],
            'generated_at' => now(),
        ];

        $this->showReport = true;

        Notification::make()->title('অগ্রগতি প্রতিবেদন তৈরি হয়েছে')->success()->send();
    }

    public function downloadPdf()
    {
        if (!$this->progressData) {
            Notification::make()->title('প্রথমে Generate করুন')->warning()->send();
            return;
        }

        $data = [
            'progressData' => $this->progressData,
            'institute' => [
                'name' => institution_name(),
                'address' => institution_address(),
                'phone' => institution_phone(),
            ],
        ];

        $pdf = Pdf::loadView('pdf.progress-report', $data)
            ->setPaper('a4', 'portrait');

        $fileName = 'progress-report-' . $this->progressData['student']->name . '-' . now()->timestamp . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }
}
