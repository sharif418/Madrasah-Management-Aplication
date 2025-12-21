<?php

namespace App\Filament\Pages;

use App\Models\Income;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\StudentFee;
use App\Models\BankAccount;
use App\Models\SalaryAdvance;
use App\Models\StaffLoan;
use App\Filament\Pages\BasePage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class BalanceSheet extends BasePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'ব্যালেন্স শীট';

    protected static ?string $title = 'ব্যালেন্স শীট (Balance Sheet)';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.balance-sheet';

    public ?array $data = [];
    public array $assets = [];
    public array $liabilities = [];
    public array $summary = [];
    public bool $showReport = false;

    public function mount(): void
    {
        $this->form->fill([
            'as_of_date' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('as_of_date')
                    ->label('তারিখ পর্যন্ত')
                    ->required()
                    ->native(false)
                    ->default(now()),
            ])
            ->columns(1)
            ->statePath('data');
    }

    public function generate(): void
    {
        $this->form->validate();

        $asOfDate = Carbon::parse($this->data['as_of_date']);

        // ASSETS (সম্পদ)

        // Cash in Hand - Fee Collections - Expenses up to date
        $totalFeeCollection = FeePayment::where('payment_date', '<=', $asOfDate)->sum('amount');
        $totalOtherIncome = Income::where('date', '<=', $asOfDate)->sum('amount');
        $totalExpense = Expense::where('date', '<=', $asOfDate)->sum('amount');
        $cashInHand = $totalFeeCollection + $totalOtherIncome - $totalExpense;

        // Bank Balance
        $bankBalance = 0;
        if (class_exists(BankAccount::class)) {
            $bankBalance = BankAccount::sum('current_balance');
        }

        // Receivables (Due Fees)
        $receivables = StudentFee::where('due_amount', '>', 0)->sum('due_amount');

        // Advance to Staff (not yet deducted)
        $staffAdvances = 0;
        if (class_exists(SalaryAdvance::class)) {
            $staffAdvances = SalaryAdvance::whereIn('status', ['paid', 'deducting'])
                ->get()
                ->sum(fn($a) => $a->remaining_amount);
        }

        // Staff Loans (not yet recovered)
        $staffLoans = 0;
        if (class_exists(StaffLoan::class)) {
            $staffLoans = StaffLoan::where('status', 'active')
                ->get()
                ->sum(fn($l) => $l->remaining_amount);
        }

        $this->assets = [
            ['name' => 'নগদ (Cash in Hand)', 'amount' => max(0, $cashInHand)],
            ['name' => 'ব্যাংক ব্যালেন্স', 'amount' => $bankBalance],
            ['name' => 'প্রাপ্য ফি (Receivables)', 'amount' => $receivables],
            ['name' => 'কর্মী অগ্রিম', 'amount' => $staffAdvances],
            ['name' => 'কর্মী ঋণ', 'amount' => $staffLoans],
        ];

        $totalAssets = collect($this->assets)->sum('amount');

        // LIABILITIES (দায়)
        // For educational institution, liabilities are minimal
        // Could include payables, security deposits, etc.
        $this->liabilities = [
            ['name' => 'প্রদেয় বিল (Payables)', 'amount' => 0],
            ['name' => 'জামানত (Security Deposits)', 'amount' => 0],
        ];

        $totalLiabilities = collect($this->liabilities)->sum('amount');

        // EQUITY (মূলধন/নিট সম্পদ)
        $netWorth = $totalAssets - $totalLiabilities;

        $this->summary = [
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'net_worth' => $netWorth,
            'as_of_date' => $asOfDate->format('d/m/Y'),
        ];

        $this->showReport = true;
    }

    public function exportPdf()
    {
        if (!$this->showReport) {
            $this->generate();
        }

        $pdf = Pdf::loadView('pdf.balance-sheet', [
            'assets' => $this->assets,
            'liabilities' => $this->liabilities,
            'summary' => $this->summary,
            'date' => now()->format('d/m/Y'),
        ]);

        return Response::streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'balance_sheet_' . date('Y-m-d') . '.pdf');
    }
}
