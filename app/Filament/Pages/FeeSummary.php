<?php

namespace App\Filament\Pages;

use App\Models\StudentFee;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\ClassName;
use App\Models\AcademicYear;
use App\Filament\Pages\BasePage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class FeeSummary extends BasePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'ফি ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'ফি সামারি';

    protected static ?string $title = 'ফি সামারি ড্যাশবোর্ড';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.fee-summary';

    public ?array $data = [];
    public array $summary = [];
    public array $classWise = [];
    public array $feeTypeWise = [];
    public array $monthlyTrend = [];
    public bool $showReport = false;

    public function mount(): void
    {
        $this->form->fill([
            'academic_year_id' => AcademicYear::current()?->id,
            'year' => now()->year,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('academic_year_id')
                    ->label('শিক্ষাবর্ষ')
                    ->options(AcademicYear::orderBy('name', 'desc')->pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->live(),

                Forms\Components\Select::make('year')
                    ->label('বছর')
                    ->options(function () {
                        $years = [];
                        for ($y = now()->year; $y >= now()->year - 5; $y--) {
                            $years[$y] = $y;
                        }
                        return $years;
                    })
                    ->required()
                    ->native(false)
                    ->live(),

                Forms\Components\Select::make('month')
                    ->label('মাস (ঐচ্ছিক)')
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
                    ->placeholder('সকল মাস')
                    ->native(false),

                Forms\Components\Select::make('class_id')
                    ->label('শ্রেণি (ঐচ্ছিক)')
                    ->options(ClassName::where('is_active', true)->orderBy('order')->pluck('name', 'id'))
                    ->placeholder('সকল শ্রেণি')
                    ->native(false),
            ])
            ->columns(4)
            ->statePath('data');
    }

    public function generate(): void
    {
        $this->form->validate();

        $year = $this->data['year'];
        $month = $this->data['month'] ?? null;
        $classId = $this->data['class_id'] ?? null;

        // Base query for student fees
        $baseQuery = StudentFee::query()
            ->where('year', $year)
            ->when($month, fn($q) => $q->where('month', $month))
            ->when($classId, fn($q) => $q->whereHas('student', fn($sq) => $sq->where('class_id', $classId)));

        // Overall Summary
        $this->summary = [
            'total_assigned' => (clone $baseQuery)->sum('final_amount'),
            'total_collected' => (clone $baseQuery)->sum('paid_amount'),
            'total_due' => (clone $baseQuery)->sum('due_amount'),
            'total_waived' => (clone $baseQuery)->where('status', 'waived')->sum('discount_amount'),
            'total_students' => (clone $baseQuery)->distinct('student_id')->count('student_id'),
            'paid_students' => (clone $baseQuery)->where('status', 'paid')->distinct('student_id')->count('student_id'),
            'due_students' => (clone $baseQuery)->whereIn('status', ['pending', 'partial'])->distinct('student_id')->count('student_id'),
        ];

        $this->summary['collection_rate'] = $this->summary['total_assigned'] > 0
            ? round(($this->summary['total_collected'] / $this->summary['total_assigned']) * 100, 1)
            : 0;

        // Class-wise Summary
        $this->classWise = StudentFee::query()
            ->select('students.class_id')
            ->selectRaw('SUM(student_fees.final_amount) as total')
            ->selectRaw('SUM(student_fees.paid_amount) as collected')
            ->selectRaw('SUM(student_fees.due_amount) as due')
            ->selectRaw('COUNT(DISTINCT student_fees.student_id) as student_count')
            ->join('students', 'students.id', '=', 'student_fees.student_id')
            ->join('classes', 'classes.id', '=', 'students.class_id')
            ->where('student_fees.year', $year)
            ->when($month, fn($q) => $q->where('student_fees.month', $month))
            ->groupBy('students.class_id')
            ->with(['student.class'])
            ->get()
            ->map(function ($item) {
                $class = ClassName::find($item->class_id);
                return [
                    'class_name' => $class?->name ?? 'Unknown',
                    'total' => $item->total,
                    'collected' => $item->collected,
                    'due' => $item->due,
                    'student_count' => $item->student_count,
                    'rate' => $item->total > 0 ? round(($item->collected / $item->total) * 100, 1) : 0,
                ];
            })
            ->toArray();

        // Fee Type-wise Summary
        $this->feeTypeWise = StudentFee::query()
            ->select('fee_structures.fee_type_id')
            ->selectRaw('SUM(student_fees.final_amount) as total')
            ->selectRaw('SUM(student_fees.paid_amount) as collected')
            ->join('fee_structures', 'fee_structures.id', '=', 'student_fees.fee_structure_id')
            ->where('student_fees.year', $year)
            ->when($month, fn($q) => $q->where('student_fees.month', $month))
            ->when($classId, fn($q) => $q->whereHas('student', fn($sq) => $sq->where('class_id', $classId)))
            ->groupBy('fee_structures.fee_type_id')
            ->get()
            ->map(function ($item) {
                $feeType = FeeType::find($item->fee_type_id);
                return [
                    'fee_type' => $feeType?->name ?? 'Unknown',
                    'total' => $item->total,
                    'collected' => $item->collected,
                    'rate' => $item->total > 0 ? round(($item->collected / $item->total) * 100, 1) : 0,
                ];
            })
            ->toArray();

        // Monthly Trend (last 12 months)
        $this->monthlyTrend = FeePayment::query()
            ->selectRaw('MONTH(payment_date) as month')
            ->selectRaw('YEAR(payment_date) as year')
            ->selectRaw('SUM(amount) as total')
            ->whereYear('payment_date', $year)
            ->groupBy(DB::raw('YEAR(payment_date), MONTH(payment_date)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $months = [
                    1 => 'জানু',
                    2 => 'ফেব্রু',
                    3 => 'মার্চ',
                    4 => 'এপ্রিল',
                    5 => 'মে',
                    6 => 'জুন',
                    7 => 'জুলাই',
                    8 => 'আগস্ট',
                    9 => 'সেপ্টে',
                    10 => 'অক্টো',
                    11 => 'নভে',
                    12 => 'ডিসে',
                ];
                return [
                    'month' => $months[$item->month] ?? $item->month,
                    'total' => $item->total,
                ];
            })
            ->toArray();

        $this->showReport = true;
    }

    public function exportPdf()
    {
        if (!$this->showReport) {
            $this->generate();
        }

        $pdf = Pdf::loadView('pdf.fee-summary', [
            'summary' => $this->summary,
            'classWise' => $this->classWise,
            'feeTypeWise' => $this->feeTypeWise,
            'monthlyTrend' => $this->monthlyTrend,
            'year' => $this->data['year'],
            'month' => $this->data['month'] ?? null,
            'date' => now()->format('d/m/Y'),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return Response::streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'fee_summary_' . $this->data['year'] . '.pdf');
    }
}
