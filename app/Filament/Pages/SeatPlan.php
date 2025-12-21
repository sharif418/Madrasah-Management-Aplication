<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\ClassName;
use App\Models\Exam;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use App\Filament\Pages\BasePage;

class SeatPlan extends BasePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationLabel = 'আসন বিন্যাস';

    protected static ?string $title = 'পরীক্ষার আসন বিন্যাস';

    protected static ?string $navigationGroup = 'পরীক্ষা ব্যবস্থাপনা';

    protected static ?int $navigationSort = 11;

    protected static string $view = 'filament.pages.seat-plan';

    public ?array $data = [];
    public ?array $seatPlanData = null;
    public bool $showPlan = false;

    public function mount(): void
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $this->form->fill([
            'academic_year_id' => $currentYear?->id,
            'students_per_room' => 30,
            'students_per_row' => 5,
            'columns_per_room' => 6,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FormSection::make('পরীক্ষা ও শ্রেণি তথ্য')
                    ->schema([
                        Select::make('academic_year_id')
                            ->label('শিক্ষাবর্ষ')
                            ->options(AcademicYear::pluck('name', 'id'))
                            ->required()
                            ->native(false)
                            ->live(),

                        Select::make('exam_id')
                            ->label('পরীক্ষা')
                            ->options(function (Get $get) {
                                $yearId = $get('academic_year_id');
                                if (!$yearId)
                                    return [];
                                return Exam::where('academic_year_id', $yearId)->pluck('name', 'id');
                            })
                            ->required()
                            ->native(false),

                        Select::make('class_ids')
                            ->label('শ্রেণি সমূহ')
                            ->options(ClassName::where('is_active', true)->orderBy('order')->pluck('name', 'id'))
                            ->multiple()
                            ->required()
                            ->native(false),
                    ])
                    ->columns(3),

                FormSection::make('রুম কনফিগারেশন')
                    ->schema([
                        TextInput::make('students_per_room')
                            ->label('প্রতি রুমে ছাত্র সংখ্যা')
                            ->numeric()
                            ->default(30)
                            ->required()
                            ->minValue(10)
                            ->maxValue(100),

                        TextInput::make('students_per_row')
                            ->label('প্রতি সারিতে ছাত্র')
                            ->numeric()
                            ->default(5)
                            ->required()
                            ->minValue(2)
                            ->maxValue(10),

                        TextInput::make('columns_per_room')
                            ->label('প্রতি রুমে কলাম সংখ্যা')
                            ->numeric()
                            ->default(6)
                            ->required()
                            ->minValue(1)
                            ->maxValue(10),

                        Repeater::make('rooms')
                            ->label('রুম সমূহ')
                            ->schema([
                                TextInput::make('room_name')
                                    ->label('রুম নম্বর/নাম')
                                    ->required()
                                    ->placeholder('যেমন: ১০১, ১০২'),
                            ])
                            ->defaultItems(3)
                            ->addActionLabel('রুম যোগ করুন')
                            ->collapsible()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function generateSeatPlan(): void
    {
        $this->form->validate();

        $examId = $this->data['exam_id'];
        $classIds = $this->data['class_ids'] ?? [];
        $studentsPerRoom = $this->data['students_per_room'] ?? 30;
        $studentsPerRow = $this->data['students_per_row'] ?? 5;
        $columnsPerRoom = $this->data['columns_per_room'] ?? 6;
        $rooms = $this->data['rooms'] ?? [];

        if (empty($classIds)) {
            Notification::make()->title('শ্রেণি নির্বাচন করুন')->warning()->send();
            return;
        }

        if (empty($rooms)) {
            Notification::make()->title('কমপক্ষে একটি রুম যোগ করুন')->warning()->send();
            return;
        }

        $exam = Exam::with('academicYear')->find($examId);

        // Get all students from selected classes
        $students = Student::whereIn('class_id', $classIds)
            ->where('status', 'active')
            ->with('class')
            ->orderBy('class_id')
            ->orderBy('roll_no')
            ->get();

        if ($students->isEmpty()) {
            Notification::make()->title('কোনো ছাত্র পাওয়া যায়নি')->warning()->send();
            return;
        }

        // Get class names
        $classes = ClassName::whereIn('id', $classIds)->pluck('name', 'id');

        // Allocate students to rooms
        $roomAllocations = [];
        $studentIndex = 0;
        $totalStudents = $students->count();

        foreach ($rooms as $roomData) {
            $roomName = $roomData['room_name'] ?? 'Room';
            $roomStudents = [];
            $rows = [];
            $currentRow = [];

            for ($i = 0; $i < $studentsPerRoom && $studentIndex < $totalStudents; $i++) {
                $student = $students[$studentIndex];
                $currentRow[] = [
                    'seat' => $i + 1,
                    'name' => $student->name,
                    'roll' => $student->roll_no,
                    'class' => $classes[$student->class_id] ?? '',
                    'student_id' => $student->student_id ?? $student->admission_no,
                ];
                $studentIndex++;

                if (count($currentRow) >= $studentsPerRow) {
                    $rows[] = $currentRow;
                    $currentRow = [];
                }
            }

            // Add remaining students in last row
            if (!empty($currentRow)) {
                $rows[] = $currentRow;
            }

            $roomAllocations[] = [
                'room_name' => $roomName,
                'rows' => $rows,
                'total_students' => array_sum(array_map('count', $rows)),
            ];
        }

        // Check if all students allocated
        $allocatedCount = array_sum(array_column($roomAllocations, 'total_students'));
        $unallocated = $totalStudents - $allocatedCount;

        $this->seatPlanData = [
            'exam' => $exam,
            'classes' => $classes->values()->toArray(),
            'rooms' => $roomAllocations,
            'total_students' => $totalStudents,
            'allocated' => $allocatedCount,
            'unallocated' => $unallocated,
            'students_per_row' => $studentsPerRow,
            'generated_at' => now(),
        ];

        $this->showPlan = true;

        if ($unallocated > 0) {
            Notification::make()
                ->title("সতর্কতা: {$unallocated} জন ছাত্র বসানো যায়নি")
                ->body('আরো রুম যোগ করুন অথবা প্রতি রুমে ছাত্র সংখ্যা বাড়ান।')
                ->warning()
                ->send();
        } else {
            Notification::make()->title('আসন বিন্যাস তৈরি হয়েছে')->success()->send();
        }
    }

    public function downloadPdf()
    {
        if (!$this->seatPlanData) {
            Notification::make()->title('প্রথমে Generate করুন')->warning()->send();
            return;
        }

        $data = [
            'seatPlanData' => $this->seatPlanData,
            'institute' => [
                'name' => institution_name(),
                'address' => institution_address(),
            ],
        ];

        $pdf = Pdf::loadView('pdf.seat-plan', $data)
            ->setPaper('a4', 'landscape');

        $fileName = 'seat-plan-' . ($this->seatPlanData['exam']->name ?? 'exam') . '-' . now()->timestamp . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }
}
