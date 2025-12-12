<?php

namespace App\Filament\Parent\Pages;

use App\Models\FeePayment;
use App\Models\Student;
use App\Models\StudentFee;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class FeesPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'ফি/বকেয়া';

    protected static ?string $title = 'ফি এবং পেমেন্ট';

    protected static ?string $slug = 'fees';

    protected static string $view = 'filament.parent.pages.fees';

    protected static ?int $navigationSort = 5;

    public ?array $data = [];

    public function mount(): void
    {
        $children = $this->getChildren();
        $this->form->fill([
            'student_id' => $children->first()?->id,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('student_id')
                    ->label('সন্তান নির্বাচন করুন')
                    ->options($this->getChildren()->pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->live(),
            ])
            ->statePath('data');
    }

    public function getChildren()
    {
        $guardian = Auth::user()->guardian;

        if (!$guardian) {
            return collect();
        }

        return Student::where('guardian_id', $guardian->id)
            ->where('status', 'active')
            ->get();
    }

    public function getFeeData(): array
    {
        $studentId = $this->data['student_id'] ?? null;

        if (!$studentId) {
            return [
                'fees' => [],
                'payments' => [],
                'summary' => [],
            ];
        }

        // Get all fees assigned to student
        $fees = StudentFee::with(['feeType', 'academicYear'])
            ->where('student_id', $studentId)
            ->orderBy('due_date', 'desc')
            ->get();

        // Get all payments
        $payments = FeePayment::with(['studentFee.feeType'])
            ->where('student_id', $studentId)
            ->orderBy('payment_date', 'desc')
            ->get();

        // Calculate summary
        $totalFees = $fees->sum('amount');
        $totalPaid = $payments->sum('amount');
        $totalDue = $fees->where('status', '!=', 'paid')->sum('amount');
        $remainingDue = max(0, $totalDue - $payments->sum('amount'));

        return [
            'fees' => $fees,
            'payments' => $payments,
            'summary' => [
                'total_fees' => $totalFees,
                'total_paid' => $totalPaid,
                'total_due' => $remainingDue,
                'pending_count' => $fees->where('status', 'pending')->count(),
                'paid_count' => $fees->where('status', 'paid')->count(),
            ],
        ];
    }
}
