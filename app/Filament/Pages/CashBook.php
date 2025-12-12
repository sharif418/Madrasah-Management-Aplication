<?php

namespace App\Filament\Pages;

use App\Models\Income;
use App\Models\Expense;
use App\Models\FeePayment;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CashBook extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'ক্যাশ বুক';

    protected static ?string $title = 'ক্যাশ বুক (দৈনিক হিসাব)';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.cash-book';

    public ?array $data = [];
    public Collection $transactions;
    public array $summary = [];
    public bool $showReport = false;

    public function mount(): void
    {
        $this->transactions = collect();
        $this->form->fill([
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
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
                    ->native(false)
                    ->default(now()->startOfMonth()),

                Forms\Components\DatePicker::make('date_to')
                    ->label('শেষ তারিখ')
                    ->required()
                    ->native(false)
                    ->default(now()),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function generate(): void
    {
        $this->form->validate();

        $dateFrom = Carbon::parse($this->data['date_from']);
        $dateTo = Carbon::parse($this->data['date_to']);

        // Get all transactions
        $transactions = collect();

        // Fee Collections (Credit)
        $feePayments = FeePayment::whereBetween('payment_date', [$dateFrom, $dateTo])
            ->with('student')
            ->get()
            ->map(fn($p) => [
                'date' => $p->payment_date,
                'type' => 'credit',
                'category' => 'ফি আদায়',
                'description' => $p->student?->name . ' - ' . $p->receipt_no,
                'amount' => $p->amount,
                'reference' => $p->receipt_no,
            ]);
        $transactions = $transactions->concat($feePayments);

        // Other Income (Credit)
        $incomes = Income::whereBetween('date', [$dateFrom, $dateTo])
            ->with('incomeHead')
            ->get()
            ->map(fn($i) => [
                'date' => $i->date,
                'type' => 'credit',
                'category' => $i->incomeHead?->name ?? 'অন্যান্য আয়',
                'description' => $i->description ?? $i->incomeHead?->name,
                'amount' => $i->amount,
                'reference' => $i->voucher_no ?? '-',
            ]);
        $transactions = $transactions->concat($incomes);

        // Expenses (Debit)
        $expenses = Expense::whereBetween('date', [$dateFrom, $dateTo])
            ->with('expenseHead')
            ->get()
            ->map(fn($e) => [
                'date' => $e->date,
                'type' => 'debit',
                'category' => $e->expenseHead?->name ?? 'অন্যান্য ব্যয়',
                'description' => $e->description ?? $e->expenseHead?->name,
                'amount' => $e->amount,
                'reference' => $e->voucher_no ?? '-',
            ]);
        $transactions = $transactions->concat($expenses);

        // Sort by date
        $this->transactions = $transactions->sortBy('date')->values();

        // Calculate summary
        $totalCredit = $this->transactions->where('type', 'credit')->sum('amount');
        $totalDebit = $this->transactions->where('type', 'debit')->sum('amount');

        // Calculate opening balance (all transactions before date_from)
        $openingCredit = FeePayment::where('payment_date', '<', $dateFrom)->sum('amount')
            + Income::where('date', '<', $dateFrom)->sum('amount');
        $openingDebit = Expense::where('date', '<', $dateFrom)->sum('amount');
        $openingBalance = $openingCredit - $openingDebit;

        $this->summary = [
            'opening_balance' => $openingBalance,
            'total_credit' => $totalCredit,
            'total_debit' => $totalDebit,
            'closing_balance' => $openingBalance + $totalCredit - $totalDebit,
            'fee_collection' => $feePayments->sum('amount'),
            'other_income' => $incomes->sum('amount'),
            'total_expense' => $expenses->sum('amount'),
            'transaction_count' => $this->transactions->count(),
        ];

        $this->showReport = true;
    }

    public function exportPdf()
    {
        if (!$this->showReport) {
            $this->generate();
        }

        $pdf = Pdf::loadView('pdf.cash-book', [
            'transactions' => $this->transactions,
            'summary' => $this->summary,
            'dateFrom' => Carbon::parse($this->data['date_from'])->format('d/m/Y'),
            'dateTo' => Carbon::parse($this->data['date_to'])->format('d/m/Y'),
            'date' => now()->format('d/m/Y'),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return Response::streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'cash_book_' . date('Y-m-d') . '.pdf');
    }
}
