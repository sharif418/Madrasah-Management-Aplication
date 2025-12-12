<?php

namespace App\Filament\Parent\Pages;

use App\Models\Attendance;
use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class AttendancePage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'হাজিরা';

    protected static ?string $title = 'হাজিরার রেকর্ড';

    protected static ?string $slug = 'attendance';

    protected static string $view = 'filament.parent.pages.attendance';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $children = $this->getChildren();
        $this->form->fill([
            'student_id' => $children->first()?->id,
            'month' => now()->month,
            'year' => now()->year,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('student_id')
                    ->label('সন্তান')
                    ->options($this->getChildren()->pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->live(),
                Select::make('month')
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
                    ->native(false)
                    ->live(),
                Select::make('year')
                    ->label('সাল')
                    ->options(array_combine(range(now()->year - 2, now()->year), range(now()->year - 2, now()->year)))
                    ->required()
                    ->native(false)
                    ->live(),
            ])
            ->columns(3)
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

    public function getAttendanceData(): array
    {
        $studentId = $this->data['student_id'] ?? null;
        $month = $this->data['month'] ?? now()->month;
        $year = $this->data['year'] ?? now()->year;

        if (!$studentId) {
            return ['records' => [], 'summary' => []];
        }

        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = now()->setYear($year)->setMonth($month)->endOfMonth();

        $records = Attendance::where('student_id', $studentId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get()
            ->keyBy(fn($a) => $a->date->format('Y-m-d'));

        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $late = $records->where('status', 'late')->count();
        $leave = $records->where('status', 'leave')->count();
        $total = $records->count();

        return [
            'records' => $records,
            'summary' => [
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'leave' => $leave,
                'total' => $total,
                'percentage' => $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0,
            ],
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }
}
