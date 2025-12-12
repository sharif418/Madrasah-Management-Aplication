<?php

namespace App\Filament\Pages;

use App\Models\Income;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\SalaryPayment;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ProfitLossStatement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'লাভ-ক্ষতি বিবরণী';

    protected static ?string $title = 'লাভ-ক্ষতি বিবরণী (Profit & Loss)';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.profit-loss-statement';

    public ?array $data = [];
    public array $incomeData = [];
    public array $expenseData = [];
    public array $summary = [];
    public bool $showReport = false;

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => now()->startOfYear()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date_from')
                    ->label('শুরুর তারিখ')
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('date_to')
                    ->label('শেষ তারিখ')
                    ->required()
                    ->native(false),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function generate(): void
    {
        $this->form->validate();

        $dateFrom = Carbon::parse($this->data['date_from']);
        $dateTo = Carbon::parse($this->data['date_to']);

        // INCOME
        $feeCollection = FeePayment::whereBetween('payment_date', [$dateFrom, $dateTo])->sum('amount');

        $otherIncomes = Income::whereBetween('date', [$dateFrom, $dateTo])
            ->with('incomeHead')
            ->get()
            ->groupBy('income_head_id')
            ->map(fn($items) => [
                'head' => $items->first()->incomeHead?->name ?? 'অন্যান্য',
                'amount' => $items->sum('amount'),
            ])
            ->values()
            ->toArray();

        $this->incomeData = [
            'fee_collection' => $feeCollection,
            'other_incomes' => $otherIncomes,
            'total_other' => collect($otherIncomes)->sum('amount'),
            'total' => $feeCollection + collect($otherIncomes)->sum('amount'),
        ];

        // EXPENSES
        $salaryExpense = SalaryPayment::whereBetween('payment_date', [$dateFrom, $dateTo])->sum('net_salary');

        $otherExpenses = Expense::whereBetween('date', [$dateFrom, $dateTo])
            ->with('expenseHead')
            ->get()
            ->groupBy('expense_head_id')
            ->map(fn($items) => [
                'head' => $items->first()->expenseHead?->name ?? 'অন্যান্য',
                'amount' => $items->sum('amount'),
            ])
            ->values()
            ->toArray();

        $this->expenseData = [
            'salary_expense' => $salaryExpense,
            'other_expenses' => $otherExpenses,
            'total_other' => collect($otherExpenses)->sum('amount'),
            'total' => $salaryExpense + collect($otherExpenses)->sum('amount'),
        ];

        // SUMMARY
        $netProfit = $this->incomeData['total'] - $this->expenseData['total'];

        $this->summary = [
            'total_income' => $this->incomeData['total'],
            'total_expense' => $this->expenseData['total'],
            'net_profit' => $netProfit,
            'is_profit' => $netProfit >= 0,
            'profit_margin' => $this->incomeData['total'] > 0
                ? round(($netProfit / $this->incomeData['total']) * 100, 1)
                : 0,
        ];

        $this->showReport = true;
    }

    public function exportPdf()
    {
        if (!$this->showReport) {
            $this->generate();
        }

        $pdf = Pdf::loadView('pdf.profit-loss', [
            'incomeData' => $this->incomeData,
            'expenseData' => $this->expenseData,
            'summary' => $this->summary,
            'dateFrom' => Carbon::parse($this->data['date_from'])->format('d/m/Y'),
            'dateTo' => Carbon::parse($this->data['date_to'])->format('d/m/Y'),
            'date' => now()->format('d/m/Y'),
        ]);

        return Response::streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'profit_loss_statement_' . date('Y-m-d') . '.pdf');
    }
}
