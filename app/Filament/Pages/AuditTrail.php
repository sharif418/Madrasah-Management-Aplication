<?php

namespace App\Filament\Pages;

use App\Models\Income;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\SalaryPayment;
use App\Models\SalaryAdvance;
use App\Models\StaffLoan;
use App\Models\FeeRefund;
use App\Filament\Pages\BasePage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class AuditTrail extends BasePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'অডিট ট্রেইল';

    protected static ?string $title = 'অডিট ট্রেইল (Audit Trail)';

    protected static ?int $navigationSort = 7;

    protected static string $view = 'filament.pages.audit-trail';

    public ?array $data = [];
    public Collection $logs;
    public bool $showReport = false;

    public function mount(): void
    {
        $this->logs = collect();
        $this->form->fill([
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            'type' => 'all',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('ধরণ')
                    ->options([
                        'all' => 'সকল',
                        'fee_payment' => 'ফি আদায়',
                        'income' => 'আয়',
                        'expense' => 'ব্যয়',
                        'salary' => 'বেতন',
                        'advance' => 'অগ্রিম',
                        'loan' => 'ঋণ',
                        'refund' => 'ফেরত',
                    ])
                    ->default('all')
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

        $dateFrom = Carbon::parse($this->data['date_from']);
        $dateTo = Carbon::parse($this->data['date_to']);
        $type = $this->data['type'];

        $logs = collect();

        // Fee Payments
        if ($type === 'all' || $type === 'fee_payment') {
            $feePayments = FeePayment::whereBetween('payment_date', [$dateFrom, $dateTo])
                ->with(['student', 'collector'])
                ->get()
                ->map(fn($p) => [
                    'datetime' => $p->created_at,
                    'type' => 'ফি আদায়',
                    'type_key' => 'fee_payment',
                    'reference' => $p->receipt_no,
                    'description' => 'ছাত্র: ' . ($p->student?->name ?? '-'),
                    'amount' => $p->amount,
                    'is_credit' => true,
                    'user' => $p->collector?->name ?? 'System',
                ]);
            $logs = $logs->concat($feePayments);
        }

        // Incomes
        if ($type === 'all' || $type === 'income') {
            $incomes = Income::whereBetween('date', [$dateFrom, $dateTo])
                ->with('incomeHead')
                ->get()
                ->map(fn($i) => [
                    'datetime' => $i->created_at,
                    'type' => 'আয়',
                    'type_key' => 'income',
                    'reference' => $i->voucher_no ?? '-',
                    'description' => $i->incomeHead?->name . ': ' . ($i->description ?? ''),
                    'amount' => $i->amount,
                    'is_credit' => true,
                    'user' => 'Admin',
                ]);
            $logs = $logs->concat($incomes);
        }

        // Expenses
        if ($type === 'all' || $type === 'expense') {
            $expenses = Expense::whereBetween('date', [$dateFrom, $dateTo])
                ->with('expenseHead')
                ->get()
                ->map(fn($e) => [
                    'datetime' => $e->created_at,
                    'type' => 'ব্যয়',
                    'type_key' => 'expense',
                    'reference' => $e->voucher_no ?? '-',
                    'description' => $e->expenseHead?->name . ': ' . ($e->description ?? ''),
                    'amount' => $e->amount,
                    'is_credit' => false,
                    'user' => 'Admin',
                ]);
            $logs = $logs->concat($expenses);
        }

        // Salary Payments
        if ($type === 'all' || $type === 'salary') {
            if (class_exists(SalaryPayment::class)) {
                $salaries = SalaryPayment::whereBetween('payment_date', [$dateFrom, $dateTo])
                    ->with('staff')
                    ->get()
                    ->map(fn($s) => [
                        'datetime' => $s->created_at,
                        'type' => 'বেতন',
                        'type_key' => 'salary',
                        'reference' => $s->payment_id ?? '-',
                        'description' => 'কর্মী: ' . ($s->staff?->name ?? '-'),
                        'amount' => $s->net_salary,
                        'is_credit' => false,
                        'user' => 'Admin',
                    ]);
                $logs = $logs->concat($salaries);
            }
        }

        // Salary Advances
        if ($type === 'all' || $type === 'advance') {
            if (class_exists(SalaryAdvance::class)) {
                $advances = SalaryAdvance::whereBetween('advance_date', [$dateFrom, $dateTo])
                    ->where('status', '!=', 'pending')
                    ->with(['staff', 'approver'])
                    ->get()
                    ->map(fn($a) => [
                        'datetime' => $a->created_at,
                        'type' => 'অগ্রিম',
                        'type_key' => 'advance',
                        'reference' => $a->advance_no,
                        'description' => 'কর্মী: ' . ($a->staff?->name ?? '-'),
                        'amount' => $a->amount,
                        'is_credit' => false,
                        'user' => $a->approver?->name ?? 'Admin',
                    ]);
                $logs = $logs->concat($advances);
            }
        }

        // Staff Loans
        if ($type === 'all' || $type === 'loan') {
            if (class_exists(StaffLoan::class)) {
                $loans = StaffLoan::whereBetween('loan_date', [$dateFrom, $dateTo])
                    ->whereIn('status', ['approved', 'active', 'completed'])
                    ->with(['staff', 'approver'])
                    ->get()
                    ->map(fn($l) => [
                        'datetime' => $l->created_at,
                        'type' => 'ঋণ',
                        'type_key' => 'loan',
                        'reference' => $l->loan_no,
                        'description' => 'কর্মী: ' . ($l->staff?->name ?? '-'),
                        'amount' => $l->loan_amount,
                        'is_credit' => false,
                        'user' => $l->approver?->name ?? 'Admin',
                    ]);
                $logs = $logs->concat($loans);
            }
        }

        // Fee Refunds
        if ($type === 'all' || $type === 'refund') {
            if (class_exists(FeeRefund::class)) {
                $refunds = FeeRefund::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->where('status', 'completed')
                    ->with(['student', 'approver'])
                    ->get()
                    ->map(fn($r) => [
                        'datetime' => $r->created_at,
                        'type' => 'ফেরত',
                        'type_key' => 'refund',
                        'reference' => $r->refund_no,
                        'description' => 'ছাত্র: ' . ($r->student?->name ?? '-'),
                        'amount' => $r->refund_amount,
                        'is_credit' => false,
                        'user' => $r->approver?->name ?? 'Admin',
                    ]);
                $logs = $logs->concat($refunds);
            }
        }

        $this->logs = $logs->sortByDesc('datetime')->values();
        $this->showReport = true;
    }
}
