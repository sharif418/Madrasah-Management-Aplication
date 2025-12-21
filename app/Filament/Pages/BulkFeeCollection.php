<?php

namespace App\Filament\Pages;

use App\Models\StudentFee;
use App\Models\FeePayment;
use App\Models\Student;
use App\Models\ClassName;
use App\Models\FeeType;
use App\Filament\Pages\BasePage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;

class BulkFeeCollection extends BasePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'ফি ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'বাল্ক ফি আদায়';

    protected static ?string $title = 'বাল্ক ফি আদায়';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.bulk-fee-collection';

    public ?array $data = [];
    public Collection $studentFees;
    public array $selectedFees = [];
    public bool $showStudents = false;
    public float $totalSelected = 0;

    public function mount(): void
    {
        $this->studentFees = collect();
        $this->form->fill([
            'month' => now()->month,
            'year' => now()->year,
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ফিল্টার')
                    ->schema([
                        Forms\Components\Grid::make(5)
                            ->schema([
                                Forms\Components\Select::make('class_id')
                                    ->label('শ্রেণি')
                                    ->options(ClassName::where('is_active', true)->orderBy('order')->pluck('name', 'id'))
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                Forms\Components\Select::make('fee_type_id')
                                    ->label('ফি টাইপ (ঐচ্ছিক)')
                                    ->options(FeeType::where('is_active', true)->pluck('name', 'id'))
                                    ->placeholder('সকল')
                                    ->native(false),

                                Forms\Components\Select::make('month')
                                    ->label('মাস')
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
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('year')
                                    ->label('বছর')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\Select::make('status_filter')
                                    ->label('স্ট্যাটাস')
                                    ->options([
                                        'pending' => 'বকেয়া',
                                        'partial' => 'আংশিক',
                                        'all_due' => 'সকল বকেয়া',
                                    ])
                                    ->default('all_due')
                                    ->native(false),
                            ]),
                    ]),

                Forms\Components\Section::make('পেমেন্ট তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\DatePicker::make('payment_date')
                                    ->label('পেমেন্টের তারিখ')
                                    ->required()
                                    ->native(false)
                                    ->default(now()),

                                Forms\Components\Select::make('payment_method')
                                    ->label('পেমেন্ট মাধ্যম')
                                    ->options([
                                        'cash' => 'নগদ',
                                        'bkash' => 'বিকাশ',
                                        'nagad' => 'নগদ (Nagad)',
                                        'bank' => 'ব্যাংক',
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('transaction_id')
                                    ->label('ট্রানজেকশন আইডি'),

                                Forms\Components\Textarea::make('remarks')
                                    ->label('মন্তব্য')
                                    ->rows(1),
                            ]),
                    ])
                    ->visible(fn() => $this->showStudents),
            ])
            ->statePath('data');
    }

    public function loadStudents(): void
    {
        $this->form->validate();

        $data = $this->data;

        if (empty($data['class_id'])) {
            Notification::make()->warning()->title('শ্রেণি নির্বাচন করুন')->send();
            return;
        }

        $query = StudentFee::query()
            ->whereHas('student', fn($q) => $q->where('class_id', $data['class_id'])->where('status', 'active'))
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->where('due_amount', '>', 0);

        if (!empty($data['fee_type_id'])) {
            $query->whereHas('feeStructure', fn($q) => $q->where('fee_type_id', $data['fee_type_id']));
        }

        $statusFilter = $data['status_filter'] ?? 'all_due';
        if ($statusFilter === 'pending') {
            $query->where('status', 'pending');
        } elseif ($statusFilter === 'partial') {
            $query->where('status', 'partial');
        }

        $this->studentFees = $query->with(['student.class', 'feeStructure.feeType'])->orderBy('student_id')->get();

        $this->selectedFees = [];
        $this->totalSelected = 0;
        $this->showStudents = true;

        Notification::make()
            ->success()
            ->title($this->studentFees->count() . ' টি বকেয়া ফি পাওয়া গেছে')
            ->send();
    }

    public function selectAll(): void
    {
        $this->selectedFees = $this->studentFees->pluck('id')->toArray();
        $this->updateTotal();
    }

    public function deselectAll(): void
    {
        $this->selectedFees = [];
        $this->totalSelected = 0;
    }

    public function updateTotal(): void
    {
        $this->totalSelected = $this->studentFees
            ->whereIn('id', $this->selectedFees)
            ->sum('due_amount');
    }

    public function collectFees(): void
    {
        if (empty($this->selectedFees)) {
            Notification::make()->warning()->title('কোন ফি নির্বাচন করা হয়নি')->send();
            return;
        }

        $data = $this->data;
        $collected = 0;
        $receipts = [];

        foreach ($this->selectedFees as $feeId) {
            $studentFee = StudentFee::find($feeId);
            if (!$studentFee || $studentFee->due_amount <= 0)
                continue;

            $payAmount = $studentFee->due_amount;

            // Create payment record
            $payment = FeePayment::create([
                'receipt_no' => $this->generateReceiptNo(),
                'student_id' => $studentFee->student_id,
                'student_fee_id' => $studentFee->id,
                'amount' => $payAmount,
                'late_fee' => 0,
                'total_amount' => $payAmount,
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'collected_by' => auth()->id(),
            ]);

            // Update student fee
            $studentFee->update([
                'paid_amount' => $studentFee->paid_amount + $payAmount,
                'due_amount' => 0,
                'status' => 'paid',
            ]);

            $receipts[] = $payment->receipt_no;
            $collected++;
        }

        Notification::make()
            ->success()
            ->title('বাল্ক ফি আদায় সম্পন্ন!')
            ->body("$collected টি ফি আদায় হয়েছে। মোট: ৳" . number_format($this->totalSelected, 2))
            ->send();

        // Reset
        $this->loadStudents();
    }

    private function generateReceiptNo(): string
    {
        $year = now()->year;
        $lastPayment = FeePayment::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $nextNumber = $lastPayment ? (int) substr($lastPayment->receipt_no, -5) + 1 : 1;

        return sprintf('RCP-%d-%05d', $year, $nextNumber);
    }

    public function exportPdf()
    {
        if ($this->studentFees->isEmpty()) {
            Notification::make()->warning()->title('কোন ডাটা নেই')->send();
            return;
        }

        $pdf = Pdf::loadView('pdf.bulk-fee-list', [
            'studentFees' => $this->studentFees,
            'className' => ClassName::find($this->data['class_id'])?->name,
            'month' => $this->data['month'],
            'year' => $this->data['year'],
            'totalDue' => $this->studentFees->sum('due_amount'),
            'date' => now()->format('d/m/Y'),
        ]);

        return Response::streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'bulk_fee_list_' . date('Y-m-d') . '.pdf');
    }
}
