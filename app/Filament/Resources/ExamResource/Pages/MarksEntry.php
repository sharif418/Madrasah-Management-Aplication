<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use App\Models\Mark;
use App\Models\Student;
use App\Models\ExamSchedule;
use App\Models\Grade;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class MarksEntry extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = ExamResource::class;

    protected static string $view = 'filament.resources.exam-resource.pages.marks-entry';

    protected static ?string $title = 'নম্বর এন্ট্রি';

    public $record;
    public ?array $data = [];
    public Collection $students;
    public Collection $schedules;
    public ?int $selectedSubject = null;
    public array $marksData = [];
    public bool $showStudents = false;
    public ?ExamSchedule $currentSchedule = null;

    public function mount($record): void
    {
        $this->record = \App\Models\Exam::findOrFail($record);
        $this->students = collect();
        $this->schedules = $this->record->schedules()->with('subject')->get();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('বিষয় নির্বাচন')
                    ->schema([
                        Forms\Components\Select::make('subject_id')
                            ->label('বিষয়')
                            ->options($this->schedules->pluck('subject.name', 'subject_id'))
                            ->required()
                            ->native(false)
                            ->live(),
                    ]),
            ])
            ->statePath('data');
    }

    public function loadStudents(): void
    {
        $data = $this->form->getState();

        if (empty($data['subject_id'])) {
            Notification::make()
                ->warning()
                ->title('বিষয় নির্বাচন করুন')
                ->send();
            return;
        }

        $this->selectedSubject = $data['subject_id'];

        // Get schedule for marks info
        $this->currentSchedule = $this->schedules->where('subject_id', $this->selectedSubject)->first();

        // Get students from the exam's class
        $this->students = Student::where('class_id', $this->record->class_id)
            ->where('status', 'active')
            ->orderBy('roll_no')
            ->orderBy('name')
            ->get();

        // Get existing marks
        $existingMarks = Mark::where('exam_id', $this->record->id)
            ->where('subject_id', $this->selectedSubject)
            ->get()
            ->keyBy('student_id');

        // Initialize marks data with proper fields
        $this->marksData = [];
        foreach ($this->students as $student) {
            $existingMark = $existingMarks->get($student->id);
            $this->marksData[$student->id] = [
                'written_marks' => $existingMark?->written_marks ?? '',
                'mcq_marks' => $existingMark?->mcq_marks ?? '',
                'practical_marks' => $existingMark?->practical_marks ?? '',
                'viva_marks' => $existingMark?->viva_marks ?? '',
                'is_absent' => $existingMark?->is_absent ?? false,
                'full_marks' => $this->currentSchedule?->full_marks ?? 100,
            ];
        }

        $this->showStudents = true;
    }

    public function saveMarks(): void
    {
        $schedule = $this->currentSchedule;
        $savedCount = 0;

        foreach ($this->marksData as $studentId => $data) {
            // Skip if all marks are empty and not absent
            $hasMarks = !empty($data['written_marks']) || !empty($data['mcq_marks']) ||
                !empty($data['practical_marks']) || !empty($data['viva_marks']);
            $isAbsent = $data['is_absent'] ?? false;

            if (!$hasMarks && !$isAbsent) {
                continue;
            }

            $student = Student::find($studentId);

            // Prepare mark data
            $markData = [
                'class_id' => $student?->class_id ?? $this->record->class_id,
                'written_marks' => $isAbsent ? 0 : ((float) ($data['written_marks'] ?: 0)),
                'mcq_marks' => $isAbsent ? 0 : ((float) ($data['mcq_marks'] ?: 0)),
                'practical_marks' => $isAbsent ? 0 : ((float) ($data['practical_marks'] ?: 0)),
                'viva_marks' => $isAbsent ? 0 : ((float) ($data['viva_marks'] ?: 0)),
                'is_absent' => $isAbsent,
                'entered_by' => auth()->id(),
            ];

            // Total marks will be auto-calculated by model boot method
            Mark::updateOrCreate(
                [
                    'exam_id' => $this->record->id,
                    'student_id' => $studentId,
                    'subject_id' => $this->selectedSubject,
                ],
                $markData
            );

            $savedCount++;
        }

        Notification::make()
            ->success()
            ->title('নম্বর সংরক্ষিত!')
            ->body("{$savedCount} জন ছাত্রের নম্বর সফলভাবে সংরক্ষণ করা হয়েছে।")
            ->send();
    }

    public function markAllAbsent(): void
    {
        foreach ($this->marksData as $studentId => $data) {
            $this->marksData[$studentId]['is_absent'] = true;
            $this->marksData[$studentId]['written_marks'] = '';
            $this->marksData[$studentId]['mcq_marks'] = '';
            $this->marksData[$studentId]['practical_marks'] = '';
            $this->marksData[$studentId]['viva_marks'] = '';
        }

        Notification::make()
            ->warning()
            ->title('সকলকে অনুপস্থিত চিহ্নিত করা হয়েছে')
            ->body('সংরক্ষণ করতে ভুলবেন না!')
            ->send();
    }

    public function clearAllAbsent(): void
    {
        foreach ($this->marksData as $studentId => $data) {
            $this->marksData[$studentId]['is_absent'] = false;
        }

        Notification::make()
            ->info()
            ->title('অনুপস্থিত চিহ্ন মুছে ফেলা হয়েছে')
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
