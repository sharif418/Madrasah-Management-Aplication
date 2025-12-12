<?php

namespace App\Filament\Student\Pages;

use App\Models\Attendance;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;

class MyAttendance extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string $view = 'filament.student.pages.my-attendance';

    protected static ?string $title = 'আমার উপস্থিতি';

    protected static ?string $navigationLabel = 'উপস্থিতি';

    protected static ?int $navigationSort = 4;

    public function table(Table $table): Table
    {
        $student = Auth::user()->student;

        return $table
            ->query(
                Attendance::query()
                    ->when($student, fn($q) => $q->where('student_id', $student->id))
                    ->when(!$student, fn($q) => $q->whereRaw('1 = 0'))
            )
            ->columns([
                TextColumn::make('date')
                    ->label('তারিখ')
                    ->date('d M, Y')
                    ->sortable(),

                TextColumn::make('day')
                    ->label('বার')
                    ->formatStateUsing(function ($record) {
                        $days = [
                            'Saturday' => 'শনিবার',
                            'Sunday' => 'রবিবার',
                            'Monday' => 'সোমবার',
                            'Tuesday' => 'মঙ্গলবার',
                            'Wednesday' => 'বুধবার',
                            'Thursday' => 'বৃহস্পতিবার',
                            'Friday' => 'শুক্রবার',
                        ];
                        return $days[$record->date->format('l')] ?? $record->date->format('l');
                    }),

                TextColumn::make('status')
                    ->label('অবস্থা')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'present' => 'উপস্থিত',
                        'absent' => 'অনুপস্থিত',
                        'late' => 'বিলম্বে',
                        'leave' => 'ছুটি',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'leave' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('check_in_time')
                    ->label('আগমন সময়')
                    ->time('h:i A')
                    ->placeholder('-'),

                TextColumn::make('remarks')
                    ->label('মন্তব্য')
                    ->placeholder('-')
                    ->limit(30),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('অবস্থা')
                    ->options([
                        'present' => 'উপস্থিত',
                        'absent' => 'অনুপস্থিত',
                        'late' => 'বিলম্বে',
                        'leave' => 'ছুটি',
                    ]),
            ])
            ->defaultSort('date', 'desc')
            ->striped()
            ->emptyStateHeading('কোন উপস্থিতি রেকর্ড নেই')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public function getMonthlyStats(): array
    {
        $student = Auth::user()->student;
        if (!$student) {
            return ['present' => 0, 'absent' => 0, 'late' => 0, 'leave' => 0, 'total' => 0, 'percentage' => 0];
        }

        $total = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        $present = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'present')
            ->count();

        $absent = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'absent')
            ->count();

        $late = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'late')
            ->count();

        $leave = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'leave')
            ->count();

        return [
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'leave' => $leave,
            'total' => $total,
            'percentage' => $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0,
        ];
    }
}
