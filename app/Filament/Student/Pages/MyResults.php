<?php

namespace App\Filament\Student\Pages;

use App\Models\ExamResult;
use App\Models\Mark;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyResults extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string $view = 'filament.student.pages.my-results';

    protected static ?string $title = 'আমার রেজাল্ট';

    protected static ?string $navigationLabel = 'রেজাল্ট';

    protected static ?int $navigationSort = 3;

    public function table(Table $table): Table
    {
        $student = Auth::user()->student;

        return $table
            ->query(
                ExamResult::query()
                    ->when($student, fn($q) => $q->where('student_id', $student->id))
                    ->when(!$student, fn($q) => $q->whereRaw('1 = 0'))
                    ->with(['exam', 'exam.examType'])
            )
            ->columns([
                TextColumn::make('exam.examType.name')
                    ->label('পরীক্ষার ধরণ')
                    ->sortable(),

                TextColumn::make('exam.name')
                    ->label('পরীক্ষার নাম')
                    ->searchable(),

                TextColumn::make('total_marks')
                    ->label('মোট নম্বর')
                    ->alignCenter(),

                TextColumn::make('obtained_marks')
                    ->label('প্রাপ্ত নম্বর')
                    ->alignCenter(),

                TextColumn::make('percentage')
                    ->label('শতকরা')
                    ->suffix('%')
                    ->alignCenter(),

                TextColumn::make('grade')
                    ->label('গ্রেড')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'A+' => 'success',
                        'A' => 'success',
                        'A-' => 'info',
                        'B' => 'warning',
                        'C' => 'warning',
                        'D' => 'danger',
                        'F' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('position')
                    ->label('অবস্থান')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('ফলাফল')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => $state === 'pass' ? 'পাস' : 'ফেল')
                    ->color(fn(string $state): string => $state === 'pass' ? 'success' : 'danger'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('কোন রেজাল্ট নেই')
            ->emptyStateDescription('আপনার পরীক্ষার ফলাফল এখানে দেখা যাবে')
            ->emptyStateIcon('heroicon-o-document-chart-bar');
    }
}
