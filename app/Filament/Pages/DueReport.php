<?php

namespace App\Filament\Pages;

use App\Models\StudentFee;
use App\Models\Student;
use App\Models\ClassName;
use App\Filament\Pages\BasePage;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class DueReport extends BasePage implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = 'ফি ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'বকেয়া রিপোর্ট';

    protected static ?string $title = 'ফি বকেয়া রিপোর্ট';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.due-report';

    public ?int $classFilter = null;
    public ?string $monthFilter = null;

    public function mount(): void
    {
        $this->monthFilter = now()->format('Y-m');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                StudentFee::query()
                    ->where(function ($q) {
                        $q->where('status', 'pending')
                            ->orWhere('status', 'partial');
                    })
                    ->where('due_amount', '>', 0)
            )
            ->columns([
                Tables\Columns\TextColumn::make('student.student_id')
                    ->label('আইডি')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('ছাত্রের নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('student.class.name')
                    ->label('শ্রেণি'),

                Tables\Columns\TextColumn::make('feeStructure.feeType.name')
                    ->label('ফি টাইপ'),

                Tables\Columns\TextColumn::make('final_amount')
                    ->label('মোট')
                    ->money('BDT'),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('পরিশোধ')
                    ->money('BDT'),

                Tables\Columns\TextColumn::make('due_amount')
                    ->label('বকেয়া')
                    ->money('BDT')
                    ->weight('bold')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('month')
                    ->label('মাস')
                    ->formatStateUsing(function ($state) {
                        $months = [
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
                        ];
                        return $months[$state] ?? $state;
                    }),

                Tables\Columns\TextColumn::make('student.guardian.phone')
                    ->label('মোবাইল')
                    ->copyable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'unpaid' => 'বকেয়া',
                        'partial' => 'আংশিক',
                        default => $state,
                    })
                    ->color(fn($state) => $state === 'unpaid' ? 'danger' : 'warning'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_id')
                    ->label('শ্রেণি')
                    ->options(ClassName::where('is_active', true)->orderBy('order')->pluck('name', 'id'))
                    ->query(
                        fn(Builder $query, array $data) =>
                        $data['value'] ? $query->whereHas('student', fn($q) => $q->where('class_id', $data['value'])) : $query
                    ),

                Tables\Filters\SelectFilter::make('fee_type_id')
                    ->label('ফি টাইপ')
                    ->options(\App\Models\FeeType::where('is_active', true)->pluck('name', 'id'))
                    ->query(
                        fn(Builder $query, array $data) =>
                        $data['value'] ? $query->whereHas('feeStructure', fn($q) => $q->where('fee_type_id', $data['value'])) : $query
                    ),

                Tables\Filters\SelectFilter::make('month')
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
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('collect')
                    ->label('আদায়')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->url(fn($record) => route('filament.admin.resources.fee-collections.create', ['student_id' => $record->student_id])),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('কোন বকেয়া নেই')
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    public function getTotalDue(): float
    {
        return StudentFee::query()
            ->where(function ($q) {
                $q->where('status', 'pending')
                    ->orWhere('status', 'partial');
            })
            ->sum('due_amount');
    }

    public function getTotalStudents(): int
    {
        return StudentFee::query()
            ->where(function ($q) {
                $q->where('status', 'pending')
                    ->orWhere('status', 'partial');
            })
            ->distinct('student_id')
            ->count('student_id');
    }

    public function exportPdf()
    {
        $dues = StudentFee::with(['student.class', 'feeStructure.feeType'])
            ->where(function ($q) {
                $q->where('status', 'pending')
                    ->orWhere('status', 'partial');
            })
            ->where('due_amount', '>', 0)
            ->get();

        $pdf = Pdf::loadView('pdf.due-report', [
            'dues' => $dues,
            'totalDue' => $this->getTotalDue(),
            'totalStudents' => $this->getTotalStudents(),
            'date' => now()->format('d/m/Y'),
        ]);

        return Response::streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'due_report_' . date('Y-m-d') . '.pdf');
    }
}
