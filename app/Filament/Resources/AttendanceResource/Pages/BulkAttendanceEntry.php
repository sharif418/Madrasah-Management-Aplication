<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\ClassName;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Services\AbsenceNotificationService;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class BulkAttendanceEntry extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = AttendanceResource::class;

    protected static string $view = 'filament.resources.attendance-resource.pages.bulk-attendance-entry';

    protected static ?string $title = 'বাল্ক উপস্থিতি এন্ট্রি';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public ?array $data = [];
    public Collection $students;
    public array $attendanceData = [];
    public array $timeData = [];
    public bool $showStudents = false;
    public bool $enableTimeEntry = false;
    public ?string $defaultInTime = null;

    public function mount(): void
    {
        $this->students = collect();
        $this->defaultInTime = institution_start_time() ?? '08:00';

        $this->form->fill([
            'date' => now()->format('Y-m-d'),
            'academic_year_id' => AcademicYear::current()?->id,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('উপস্থিতি এন্ট্রি')
                    ->description('শ্রেণি ও তারিখ নির্বাচন করুন')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\DatePicker::make('date')
                                    ->label('তারিখ')
                                    ->required()
                                    ->default(now())
                                    ->native(false)
                                    ->maxDate(now()),

                                Forms\Components\Select::make('academic_year_id')
                                    ->label('শিক্ষাবর্ষ')
                                    ->options(AcademicYear::pluck('name', 'id'))
                                    ->default(fn() => AcademicYear::current()?->id)
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('class_id')
                                    ->label('শ্রেণি')
                                    ->options(ClassName::where('is_active', true)->orderBy('order')->pluck('name', 'id'))
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(fn(Forms\Set $set) => $set('section_id', null)),

                                Forms\Components\Select::make('section_id')
                                    ->label('শাখা')
                                    ->options(function (Forms\Get $get) {
                                        $classId = $get('class_id');
                                        if (!$classId)
                                            return [];
                                        return Section::where('class_id', $classId)
                                            ->where('is_active', true)
                                            ->pluck('name', 'id');
                                    })
                                    ->native(false),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function loadStudents(): void
    {
        $data = $this->form->getState();

        if (empty($data['class_id']) || empty($data['date'])) {
            Notification::make()
                ->warning()
                ->title('দয়া করে শ্রেণি এবং তারিখ নির্বাচন করুন')
                ->send();
            return;
        }

        $query = Student::where('class_id', $data['class_id'])
            ->where('status', 'active')
            ->orderBy('roll_no')
            ->orderBy('name');

        if (!empty($data['section_id'])) {
            $query->where('section_id', $data['section_id']);
        }

        $this->students = $query->get();

        // Check existing attendance for this date
        $existingAttendance = Attendance::where('class_id', $data['class_id'])
            ->whereDate('date', $data['date'])
            ->when(!empty($data['section_id']), fn($q) => $q->where('section_id', $data['section_id']))
            ->get()
            ->keyBy('student_id');

        // Initialize attendance data
        $this->attendanceData = [];
        $this->timeData = [];

        foreach ($this->students as $student) {
            $existing = $existingAttendance->get($student->id);
            $this->attendanceData[$student->id] = $existing?->status ?? 'present';
            $this->timeData[$student->id] = [
                'in_time' => $existing?->in_time ? \Carbon\Carbon::parse($existing->in_time)->format('H:i') : null,
                'out_time' => $existing?->out_time ? \Carbon\Carbon::parse($existing->out_time)->format('H:i') : null,
            ];
        }

        $this->showStudents = true;

        if ($this->students->isEmpty()) {
            Notification::make()
                ->warning()
                ->title('এই শ্রেণিতে কোন সক্রিয় ছাত্র নেই')
                ->send();
        }
    }

    public function toggleTimeEntry(): void
    {
        $this->enableTimeEntry = !$this->enableTimeEntry;
    }

    public function markAll(string $status): void
    {
        foreach ($this->students as $student) {
            $this->attendanceData[$student->id] = $status;
        }
    }

    public function setDefaultTime(): void
    {
        $currentTime = now()->format('H:i');
        foreach ($this->students as $student) {
            if ($this->attendanceData[$student->id] !== 'absent') {
                $this->timeData[$student->id]['in_time'] = $currentTime;
            }
        }

        Notification::make()
            ->info()
            ->title('সময় সেট হয়েছে')
            ->body("সকল উপস্থিত ছাত্রের প্রবেশ সময়: {$currentTime}")
            ->send();
    }

    public function saveAttendance(bool $sendSms = false): void
    {
        $data = $this->form->getState();
        $absentStudentIds = [];

        foreach ($this->attendanceData as $studentId => $status) {
            $timeInfo = $this->timeData[$studentId] ?? [];

            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $data['date'],
                ],
                [
                    'class_id' => $data['class_id'],
                    'section_id' => $data['section_id'] ?? null,
                    'academic_year_id' => $data['academic_year_id'],
                    'status' => $status,
                    'in_time' => $timeInfo['in_time'] ?? null,
                    'out_time' => $timeInfo['out_time'] ?? null,
                    'marked_by' => auth()->id(),
                ]
            );

            if ($status === 'absent') {
                $absentStudentIds[] = $studentId;
            }
        }

        $presentCount = collect($this->attendanceData)->filter(fn($s) => $s === 'present')->count();
        $absentCount = count($absentStudentIds);
        $lateCount = collect($this->attendanceData)->filter(fn($s) => $s === 'late')->count();

        $message = "মোট: {$this->students->count()}, উপস্থিত: {$presentCount}, অনুপস্থিত: {$absentCount}";
        if ($lateCount > 0) {
            $message .= ", বিলম্বে: {$lateCount}";
        }

        Notification::make()
            ->success()
            ->title('উপস্থিতি সংরক্ষিত!')
            ->body($message)
            ->send();

        // Send SMS if requested
        if ($sendSms && $absentCount > 0) {
            $this->sendAbsenceSms($data['class_id'], $data['date']);
        }
    }

    public function saveAndSendSms(): void
    {
        $this->saveAttendance(sendSms: true);
    }

    protected function sendAbsenceSms(int $classId, string $date): void
    {
        $service = new AbsenceNotificationService();

        if (!$service->isEnabled()) {
            Notification::make()
                ->warning()
                ->title('SMS Gateway সক্রিয় নেই')
                ->body('SMS পাঠাতে SMS Gateway কনফিগার করুন।')
                ->send();
            return;
        }

        $result = $service->sendBulkAbsenceSms($classId, $date);

        Notification::make()
            ->info()
            ->title('SMS পাঠানো হয়েছে')
            ->body("সফল: {$result['sent']}, ব্যর্থ: {$result['failed']}, স্কিপ: {$result['skipped']}")
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
