<?php

namespace App\Filament\Pages;

use App\Models\Kitab;
use App\Models\KitabProgress;
use App\Models\Student;
use App\Models\ClassName;
use App\Models\AcademicYear;
use App\Filament\Pages\BasePage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class KitabProgressDashboard extends BasePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'কিতাব প্রগ্রেস';

    protected static ?string $navigationLabel = 'প্রগ্রেস ড্যাশবোর্ড';

    protected static ?string $title = 'কিতাব প্রগ্রেস ড্যাশবোর্ড';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.kitab-progress-dashboard';

    public ?array $data = [];
    public Collection $students;
    public array $kitabSummary = [];
    public array $overallStats = [];
    public bool $showDashboard = false;

    public function mount(): void
    {
        $this->students = collect();
        $this->form->fill([
            'class_id' => null,
            'kitab_id' => null,
            'academic_year_id' => AcademicYear::where('is_current', true)->first()?->id,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('class_id')
                    ->label('ক্লাস')
                    ->options(ClassName::pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(fn(Forms\Set $set) => $set('kitab_id', null)),

                Forms\Components\Select::make('kitab_id')
                    ->label('কিতাব')
                    ->options(function (Forms\Get $get) {
                        $classId = $get('class_id');
                        if ($classId) {
                            return Kitab::where('class_id', $classId)->active()->pluck('name', 'id');
                        }
                        return Kitab::active()->pluck('name', 'id');
                    })
                    ->native(false),

                Forms\Components\Select::make('academic_year_id')
                    ->label('শিক্ষাবর্ষ')
                    ->options(AcademicYear::orderBy('id', 'desc')->pluck('name', 'id'))
                    ->native(false),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function generate(): void
    {
        $this->form->validate();

        $classId = $this->data['class_id'];
        $kitabId = $this->data['kitab_id'] ?? null;
        $yearId = $this->data['academic_year_id'] ?? null;

        // Get students
        $this->students = Student::where('class_id', $classId)
            ->where('status', 'active')
            ->with([
                'kitabProgress' => function ($q) use ($kitabId, $yearId) {
                    if ($kitabId) {
                        $q->where('kitab_id', $kitabId);
                    }
                    if ($yearId) {
                        $q->where('academic_year_id', $yearId);
                    }
                }
            ])
            ->get()
            ->map(function ($student) use ($kitabId) {
                $progress = $student->kitabProgress;
                $totalPages = $progress->sum('pages_read');
                $totalLessons = $progress->unique('lesson')->count();
                $totalChapters = $progress->unique('chapter')->count();
                $lastProgress = $progress->sortByDesc('date')->first();

                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'roll' => $student->roll,
                    'total_pages' => $totalPages,
                    'total_lessons' => $totalLessons,
                    'total_chapters' => $totalChapters,
                    'last_date' => $lastProgress?->date?->format('d M Y'),
                    'last_lesson' => $lastProgress?->lesson,
                    'status' => $lastProgress?->status ?? 'not_started',
                ];
            });

        // Kitab summary
        $kitabs = Kitab::where('class_id', $classId)->active()->get();
        $this->kitabSummary = $kitabs->map(function ($kitab) use ($yearId) {
            $progress = KitabProgress::where('kitab_id', $kitab->id);
            if ($yearId) {
                $progress->where('academic_year_id', $yearId);
            }
            $progressData = $progress->get();

            $completedStudents = $progressData->where('status', 'completed')->unique('student_id')->count();
            $avgPages = $progressData->avg('pages_read') ?? 0;

            return [
                'name' => $kitab->name,
                'total_chapters' => $kitab->total_chapters,
                'total_lessons' => $kitab->total_lessons,
                'completed_students' => $completedStudents,
                'avg_pages' => round($avgPages, 1),
            ];
        })->toArray();

        // Overall stats
        $this->overallStats = [
            'total_students' => $this->students->count(),
            'active_progress' => $this->students->where('status', 'in_progress')->count(),
            'completed' => $this->students->where('status', 'completed')->count(),
            'not_started' => $this->students->where('status', 'not_started')->count(),
            'total_pages' => $this->students->sum('total_pages'),
        ];

        $this->showDashboard = true;
    }

    public function exportPdf()
    {
        if (!$this->showDashboard) {
            $this->generate();
        }

        $className = ClassName::find($this->data['class_id'])?->name ?? '';

        $pdf = Pdf::loadView('pdf.kitab-progress', [
            'students' => $this->students,
            'kitabSummary' => $this->kitabSummary,
            'overallStats' => $this->overallStats,
            'className' => $className,
            'date' => now()->format('d/m/Y'),
        ]);

        return Response::streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'kitab_progress_' . date('Y-m-d') . '.pdf');
    }
}
