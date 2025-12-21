<?php

namespace App\Filament\Pages;

use App\Models\Income;
use App\Models\Expense;
use App\Models\IncomeHead;
use App\Models\ExpenseHead;
use App\Filament\Pages\BasePage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class Ledger extends BasePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'খতিয়ান (Ledger)';

    protected static ?string $title = 'খতিয়ান (Ledger)';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.ledger';

    public ?array $data = [];
    public Collection $entries;
    public array $summary = [];
    public bool $showReport = false;
    public string $headName = '';

    public function mount(): void
    {
        $this->entries = collect();
        $this->form->fill([
            'type' => 'expense',
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('ধরণ')
                    ->options([
                        'income' => 'আয় (Income)',
                        'expense' => 'ব্যয় (Expense)',
                    ])
                    ->required()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(fn(Forms\Set $set) => $set('head_id', null)),

                Forms\Components\Select::make('head_id')
                    ->label('হেড নির্বাচন')
                    ->options(function (Forms\Get $get) {
                        $type = $get('type');
                        if ($type === 'income') {
                            return IncomeHead::where('is_active', true)->pluck('name', 'id');
                        }
                        return ExpenseHead::where('is_active', true)->pluck('name', 'id');
                    })
                    ->required()
                    ->native(false)
                    ->searchable(),

                Forms\Components\DatePicker::make('date_from')
                    ->label('শুরুর তারিখ')
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('date_to')
                    ->label('শেষ তারিখ')
                    ->required()
                    ->native(false),
            ])
            ->columns(4)
            ->statePath('data');
    }

    public function generate(): void
    {
        $this->form->validate();

        $type = $this->data['type'];
        $headId = $this->data['head_id'];
        $dateFrom = Carbon::parse($this->data['date_from']);
        $dateTo = Carbon::parse($this->data['date_to']);

        if ($type === 'income') {
            $head = IncomeHead::find($headId);
            $this->headName = $head?->name ?? 'Unknown';

            $this->entries = Income::where('income_head_id', $headId)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->orderBy('date')
                ->get()
                ->map(fn($e) => [
                    'date' => $e->date,
                    'description' => $e->description,
                    'voucher' => $e->voucher_no ?? '-',
                    'debit' => 0,
                    'credit' => $e->amount,
                ]);
        } else {
            $head = ExpenseHead::find($headId);
            $this->headName = $head?->name ?? 'Unknown';

            $this->entries = Expense::where('expense_head_id', $headId)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->orderBy('date')
                ->get()
                ->map(fn($e) => [
                    'date' => $e->date,
                    'description' => $e->description,
                    'voucher' => $e->voucher_no ?? '-',
                    'debit' => $e->amount,
                    'credit' => 0,
                ]);
        }

        $totalDebit = $this->entries->sum('debit');
        $totalCredit = $this->entries->sum('credit');

        $this->summary = [
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'balance' => $totalCredit - $totalDebit,
            'count' => $this->entries->count(),
        ];

        $this->showReport = true;
    }

    public function exportPdf()
    {
        if (!$this->showReport) {
            $this->generate();
        }

        $pdf = Pdf::loadView('pdf.ledger', [
            'entries' => $this->entries,
            'summary' => $this->summary,
            'headName' => $this->headName,
            'type' => $this->data['type'] === 'income' ? 'আয়' : 'ব্যয়',
            'dateFrom' => Carbon::parse($this->data['date_from'])->format('d/m/Y'),
            'dateTo' => Carbon::parse($this->data['date_to'])->format('d/m/Y'),
            'date' => now()->format('d/m/Y'),
        ]);

        return Response::streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'ledger_' . date('Y-m-d') . '.pdf');
    }
}
