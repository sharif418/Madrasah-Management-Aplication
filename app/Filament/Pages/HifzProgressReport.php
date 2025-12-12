<?php

namespace App\Filament\Pages;

use App\Models\HifzProgress;
use App\Models\Student;
use App\Models\ClassName;
use App\Models\AcademicYear;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;

class HifzProgressReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'হিফজ ও কিতাব';

    protected static ?string $navigationLabel = 'হিফজ রিপোর্ট';

    protected static ?string $title = 'হিফজ প্রগ্রেস রিপোর্ট';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.hifz-progress-report';

    public ?array $data = [];
    public Collection $students;
    public array $summary = [];
    public bool $showReport = false;

    public function mount(): void
    {
        $this->students = collect();
        $this->form->fill([
            'class_id' => null,
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
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
                    ->native(false),

                Forms\Components\DatePicker::make('date_from')
                    ->label('শুরুর তারিখ')
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('date_to')
                    ->label('শেষ তারিখ')
                    ->required()
                    ->native(false),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function generate(): void
    {
        $this->form->validate();

        $classId = $this->data['class_id'];
        $dateFrom = $this->data['date_from'];
        $dateTo = $this->data['date_to'];

        $students = Student::where('class_id', $classId)
            ->where('status', 'active')
            ->with([
                'hifzProgress' => function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('date', [$dateFrom, $dateTo]);
                }
            ])
            ->orderBy('roll')
            ->get();

        $this->students = $students->map(function ($student) {
            $progress = $student->hifzProgress;

            // Calculate current para
            $lastSabaq = $progress->whereNotNull('sabaq_para')->sortByDesc('date')->first();
            $currentPara = $lastSabaq?->sabaq_para ?? 0;

            // Calculate totals
            $totalSabaqDays = $progress->whereNotNull('sabaq_para')->count();
            $totalSabqiDays = $progress->whereNotNull('sabqi_para')->count();
            $totalManzilDays = $progress->whereNotNull('manzil_para_from')->count();
            $totalLines = $progress->sum('sabaq_lines');

            // Quality average
            $sabaqQualities = $progress->whereNotNull('sabaq_quality')->pluck('sabaq_quality');
            $avgQuality = $this->calculateAverageQuality($sabaqQualities);

            // Completed paras (unique paras with completed status)
            $completedParas = $progress->whereNotNull('sabaq_para')
                ->where('sabaq_quality', '!=', 'poor')
                ->pluck('sabaq_para')
                ->unique()
                ->count();

            return [
                'id' => $student->id,
                'name' => $student->name,
                'roll' => $student->roll,
                'current_para' => $currentPara,
                'completed_paras' => $completedParas,
                'total_sabaq_days' => $totalSabaqDays,
                'total_sabqi_days' => $totalSabqiDays,
                'total_manzil_days' => $totalManzilDays,
                'total_lines' => $totalLines,
                'avg_quality' => $avgQuality,
                'progress_percentage' => round(($completedParas / 30) * 100, 1),
            ];
        });

        // Summary
        $this->summary = [
            'total_students' => $this->students->count(),
            'avg_para' => round($this->students->avg('current_para'), 1),
            'total_lines' => $this->students->sum('total_lines'),
            'completed_hifz' => $this->students->where('completed_paras', '>=', 30)->count(),
        ];

        $this->showReport = true;
    }

    protected function calculateAverageQuality($qualities): string
    {
        if ($qualities->isEmpty())
            return '-';

        $scores = $qualities->map(fn($q) => match ($q) {
            'excellent' => 4,
            'good' => 3,
            'average' => 2,
            'poor' => 1,
            default => 0,
        })->avg();

        return match (true) {
            $scores >= 3.5 => 'অতি উত্তম',
            $scores >= 2.5 => 'উত্তম',
            $scores >= 1.5 => 'মধ্যম',
            default => 'দুর্বল',
        };
    }

    public function exportPdf()
    {
        if (!$this->showReport) {
            $this->generate();
        }

        $className = ClassName::find($this->data['class_id'])?->name ?? '';

        $pdf = Pdf::loadView('pdf.hifz-report', [
            'students' => $this->students,
            'summary' => $this->summary,
            'className' => $className,
            'dateFrom' => $this->data['date_from'],
            'dateTo' => $this->data['date_to'],
            'date' => now()->format('d/m/Y'),
        ]);

        return Response::streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'hifz_report_' . date('Y-m-d') . '.pdf');
    }
}
