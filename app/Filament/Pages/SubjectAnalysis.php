<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\ClassName;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Section;
use App\Models\Subject;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use App\Filament\Pages\BasePage;
use Illuminate\Support\Facades\DB;

class SubjectAnalysis extends BasePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'বিষয় বিশ্লেষণ';

    protected static ?string $title = 'বিষয়ভিত্তিক পারফরম্যান্স বিশ্লেষণ';

    protected static ?string $navigationGroup = 'পরীক্ষা ব্যবস্থাপনা';

    protected static ?int $navigationSort = 12;

    protected static string $view = 'filament.pages.subject-analysis';

    public ?array $data = [];
    public ?array $analysisData = null;
    public bool $showAnalysis = false;

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

                Select::make('exam_id')
                    ->label('পরীক্ষা')
                    ->options(function (Get $get) {
                        $yearId = $get('academic_year_id');
                        if (!$yearId)
                            return [];
                        return Exam::where('academic_year_id', $yearId)->pluck('name', 'id');
                    })
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
                    ->native(false),

                Select::make('subject_id')
                    ->label('বিষয় (ঐচ্ছিক)')
                    ->options(function (Get $get) {
                        $classId = $get('class_id');
                        if (!$classId) {
                            return Subject::where('is_active', true)->pluck('name', 'id');
                        }
                        return Subject::where('is_active', true)
                            ->whereHas('classes', fn($q) => $q->where('class_id', $classId))
                            ->pluck('name', 'id');
                    })
                    ->helperText('খালি রাখলে সকল বিষয়ের বিশ্লেষণ দেখাবে')
                    ->native(false)
                    ->live(),
            ])
            ->columns(5)
            ->statePath('data');
    }

    public function analyze(): void
    {
        $this->form->validate();

        $examId = $this->data['exam_id'];
        $classId = $this->data['class_id'];
        $sectionId = $this->data['section_id'] ?? null;
        $subjectId = $this->data['subject_id'] ?? null;

        $exam = Exam::with('academicYear')->find($examId);
        $className = ClassName::find($classId);

        // Build marks query
        $marksQuery = Mark::with(['student', 'subject', 'examSchedule'])
            ->where('exam_id', $examId)
            ->where('class_id', $classId);

        if ($sectionId) {
            $marksQuery->whereHas('student', fn($q) => $q->where('section_id', $sectionId));
        }

        if ($subjectId) {
            $marksQuery->where('subject_id', $subjectId);
        }

        $marks = $marksQuery->get();

        if ($marks->isEmpty()) {
            Notification::make()->title('কোনো ডাটা পাওয়া যায়নি')->warning()->send();
            return;
        }

        // Group by subject
        $subjectWiseData = [];

        $groupedMarks = $marks->groupBy('subject_id');

        foreach ($groupedMarks as $subjId => $subjectMarks) {
            $subject = $subjectMarks->first()->subject;
            $examSchedule = $subjectMarks->first()->examSchedule;
            $fullMarks = $examSchedule?->full_marks ?? 100;
            $passMarks = $examSchedule?->pass_marks ?? 33;

            $totalStudents = $subjectMarks->count();
            $absentCount = $subjectMarks->where('is_absent', true)->count();
            $presentStudents = $subjectMarks->where('is_absent', false);

            $passedCount = $presentStudents->filter(fn($m) => ($m->total_marks ?? 0) >= $passMarks)->count();
            $failedCount = $presentStudents->count() - $passedCount;

            $allMarks = $presentStudents->pluck('total_marks')->filter()->values();
            $highest = $allMarks->max() ?? 0;
            $lowest = $allMarks->min() ?? 0;
            $average = round($allMarks->avg() ?? 0, 2);

            // Grade distribution
            $gradeDistribution = [
                'A+' => 0,
                'A' => 0,
                'A-' => 0,
                'B' => 0,
                'C' => 0,
                'D' => 0,
                'F' => 0
            ];

            foreach ($presentStudents as $m) {
                $percentage = ($m->total_marks / $fullMarks) * 100;
                if ($percentage >= 80)
                    $gradeDistribution['A+']++;
                elseif ($percentage >= 70)
                    $gradeDistribution['A']++;
                elseif ($percentage >= 60)
                    $gradeDistribution['A-']++;
                elseif ($percentage >= 50)
                    $gradeDistribution['B']++;
                elseif ($percentage >= 40)
                    $gradeDistribution['C']++;
                elseif ($percentage >= 33)
                    $gradeDistribution['D']++;
                else
                    $gradeDistribution['F']++;
            }

            // Top performers
            $topPerformers = $presentStudents
                ->sortByDesc('total_marks')
                ->take(5)
                ->map(fn($m) => [
                    'name' => $m->student?->name ?? 'Unknown',
                    'roll' => $m->student?->roll_no,
                    'marks' => $m->total_marks,
                    'percentage' => round(($m->total_marks / $fullMarks) * 100, 1),
                ])
                ->values()
                ->toArray();

            $subjectWiseData[] = [
                'subject_id' => $subjId,
                'subject_name' => $subject?->name ?? 'Unknown',
                'full_marks' => $fullMarks,
                'pass_marks' => $passMarks,
                'total_students' => $totalStudents,
                'present' => $presentStudents->count(),
                'absent' => $absentCount,
                'passed' => $passedCount,
                'failed' => $failedCount,
                'pass_percentage' => $presentStudents->count() > 0 ? round(($passedCount / $presentStudents->count()) * 100, 1) : 0,
                'highest' => $highest,
                'lowest' => $lowest,
                'average' => $average,
                'grade_distribution' => $gradeDistribution,
                'top_performers' => $topPerformers,
            ];
        }

        // Overall statistics
        $totalPassed = collect($subjectWiseData)->sum('passed');
        $totalFailed = collect($subjectWiseData)->sum('failed');
        $overallAverage = collect($subjectWiseData)->avg('average');

        $this->analysisData = [
            'exam' => $exam,
            'class' => $className,
            'section' => $sectionId ? Section::find($sectionId)?->name : null,
            'subjects' => $subjectWiseData,
            'overall' => [
                'total_subjects' => count($subjectWiseData),
                'average_pass_rate' => collect($subjectWiseData)->avg('pass_percentage'),
                'overall_average' => round($overallAverage, 2),
            ],
            'generated_at' => now(),
        ];

        $this->showAnalysis = true;

        Notification::make()->title('বিশ্লেষণ সম্পন্ন হয়েছে')->success()->send();
    }

    public function downloadPdf()
    {
        if (!$this->analysisData) {
            Notification::make()->title('প্রথমে বিশ্লেষণ করুন')->warning()->send();
            return;
        }

        $data = [
            'analysisData' => $this->analysisData,
            'institute' => [
                'name' => institution_name(),
                'address' => institution_address(),
            ],
        ];

        $pdf = Pdf::loadView('pdf.subject-analysis', $data)
            ->setPaper('a4', 'portrait');

        $fileName = 'subject-analysis-' . ($this->analysisData['exam']->name ?? 'exam') . '-' . now()->timestamp . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }
}
