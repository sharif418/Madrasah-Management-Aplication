<?php

namespace App\Filament\Student\Pages;

use App\Models\FeePayment;
use App\Models\StudentFee;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MyFees extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static string $view = 'filament.student.pages.my-fees';

    protected static ?string $title = 'আমার ফি';

    protected static ?string $navigationLabel = 'ফি';

    protected static ?int $navigationSort = 5;

    public function table(Table $table): Table
    {
        $student = Auth::user()->student;

        return $table
            ->query(
                StudentFee::query()
                    ->when($student, fn($q) => $q->where('student_id', $student->id))
                    ->when(!$student, fn($q) => $q->whereRaw('1 = 0'))
                    ->with(['feeType', 'academicYear'])
            )
            ->columns([
                TextColumn::make('feeType.name')
                    ->label('ফি এর ধরণ')
                    ->searchable(),

                TextColumn::make('academicYear.name')
                    ->label('শিক্ষাবর্ষ'),

                TextColumn::make('month')
                    ->label('মাস')
                    ->formatStateUsing(function ($state) {
                        $months = [
                            'january' => 'জানুয়ারি',
                            'february' => 'ফেব্রুয়ারি',
                            'march' => 'মার্চ',
                            'april' => 'এপ্রিল',
                            'may' => 'মে',
                            'june' => 'জুন',
                            'july' => 'জুলাই',
                            'august' => 'আগস্ট',
                            'september' => 'সেপ্টেম্বর',
                            'october' => 'অক্টোবর',
                            'november' => 'নভেম্বর',
                            'december' => 'ডিসেম্বর',
                        ];
                        return $months[$state] ?? $state;
                    })
                    ->placeholder('-'),

                TextColumn::make('amount')
                    ->label('পরিমাণ')
                    ->money('BDT')
                    ->alignEnd(),

                TextColumn::make('discount')
                    ->label('ছাড়')
                    ->money('BDT')
                    ->alignEnd()
                    ->placeholder('০'),

                TextColumn::make('paid_amount')
                    ->label('পরিশোধিত')
                    ->money('BDT')
                    ->alignEnd()
                    ->color('success'),

                TextColumn::make('due_amount')
                    ->label('বকেয়া')
                    ->money('BDT')
                    ->alignEnd()
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success'),

                TextColumn::make('status')
                    ->label('অবস্থা')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'paid' => 'পরিশোধিত',
                        'partial' => 'আংশিক',
                        'unpaid' => 'অপরিশোধিত',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'unpaid' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('due_date')
                    ->label('শেষ তারিখ')
                    ->date('d M, Y')
                    ->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('কোন ফি নেই')
            ->emptyStateIcon('heroicon-o-banknotes');
    }

    public function getFeesSummary(): array
    {
        $student = Auth::user()->student;
        if (!$student) {
            return ['total' => 0, 'paid' => 0, 'due' => 0, 'discount' => 0];
        }

        $fees = StudentFee::where('student_id', $student->id)->get();

        $total = $fees->sum('amount');
        $discount = $fees->sum('discount');
        $paid = $fees->sum('paid_amount');
        $due = $fees->sum('due_amount');

        return [
            'total' => $total,
            'paid' => $paid,
            'due' => $due,
            'discount' => $discount,
        ];
    }

    public function getPaymentHistory()
    {
        $student = Auth::user()->student;
        if (!$student) {
            return collect();
        }

        return FeePayment::where('student_id', $student->id)
            ->with(['feeType'])
            ->orderBy('payment_date', 'desc')
            ->take(10)
            ->get();
    }
}
