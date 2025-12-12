<?php

namespace App\Filament\Pages;

use App\Models\HifzProgress;
use App\Models\Student;
use App\Models\ClassName;
use App\Models\AcademicYear;
use App\Models\Teacher;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class HifzDailyEntry extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationGroup = 'হিফজ ও কিতাব';

    protected static ?string $navigationLabel = 'দৈনিক হিফজ এন্ট্রি';

    protected static ?string $title = 'দৈনিক হিফজ প্রগ্রেস এন্ট্রি';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.hifz-daily-entry';

    public ?array $data = [];
    public Collection $students;
    public array $entries = [];
    public bool $showForm = false;

    public function mount(): void
    {
        $this->students = collect();
        $this->form->fill([
            'date' => now()->format('Y-m-d'),
            'class_id' => null,
            'academic_year_id' => AcademicYear::where('is_current', true)->first()?->id,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label('তারিখ')
                    ->required()
                    ->default(now())
                    ->native(false),

                Forms\Components\Select::make('class_id')
                    ->label('ক্লাস')
                    ->options(ClassName::pluck('name', 'id'))
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('academic_year_id')
                    ->label('শিক্ষাবর্ষ')
                    ->options(AcademicYear::orderBy('id', 'desc')->pluck('name', 'id'))
                    ->native(false),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function loadStudents(): void
    {
        $classId = $this->data['class_id'];
        $date = $this->data['date'];

        $this->students = Student::where('class_id', $classId)
            ->where('status', 'active')
            ->orderBy('roll')
            ->get();

        // Load existing entries
        $existingEntries = HifzProgress::where('date', $date)
            ->whereIn('student_id', $this->students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        $this->entries = [];
        foreach ($this->students as $student) {
            $existing = $existingEntries->get($student->id);
            $this->entries[$student->id] = [
                'sabaq_para' => $existing?->sabaq_para,
                'sabaq_surah' => $existing?->sabaq_surah,
                'sabaq_lines' => $existing?->sabaq_lines,
                'sabaq_quality' => $existing?->sabaq_quality,
                'sabqi_para' => $existing?->sabqi_para,
                'sabqi_quality' => $existing?->sabqi_quality,
                'manzil_para_from' => $existing?->manzil_para_from,
                'manzil_para_to' => $existing?->manzil_para_to,
                'manzil_quality' => $existing?->manzil_quality,
                'tajweed_lesson' => $existing?->tajweed_lesson,
                'tajweed_quality' => $existing?->tajweed_quality,
                'qirat_surah' => $existing?->qirat_surah,
                'qirat_quality' => $existing?->qirat_quality,
                'teacher_remarks' => $existing?->teacher_remarks,
            ];
        }

        $this->showForm = true;

        Notification::make()
            ->success()
            ->title($this->students->count() . ' জন ছাত্র পাওয়া গেছে')
            ->send();
    }

    public function saveEntries(): void
    {
        $date = $this->data['date'];
        $yearId = $this->data['academic_year_id'];
        $teacherId = auth()->user()?->teacher?->id ?? null;
        $savedCount = 0;

        foreach ($this->entries as $studentId => $entry) {
            // Skip if all empty
            if (empty($entry['sabaq_para']) && empty($entry['sabqi_para']) && empty($entry['manzil_para_from'])) {
                continue;
            }

            HifzProgress::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $date,
                ],
                [
                    'academic_year_id' => $yearId,
                    'sabaq_para' => $entry['sabaq_para'] ?? null,
                    'sabaq_surah' => $entry['sabaq_surah'] ?? null,
                    'sabaq_lines' => $entry['sabaq_lines'] ?? null,
                    'sabaq_quality' => $entry['sabaq_quality'] ?? null,
                    'sabqi_para' => $entry['sabqi_para'] ?? null,
                    'sabqi_quality' => $entry['sabqi_quality'] ?? null,
                    'manzil_para_from' => $entry['manzil_para_from'] ?? null,
                    'manzil_para_to' => $entry['manzil_para_to'] ?? null,
                    'manzil_quality' => $entry['manzil_quality'] ?? null,
                    'tajweed_lesson' => $entry['tajweed_lesson'] ?? null,
                    'tajweed_quality' => $entry['tajweed_quality'] ?? null,
                    'qirat_surah' => $entry['qirat_surah'] ?? null,
                    'qirat_quality' => $entry['qirat_quality'] ?? null,
                    'teacher_remarks' => $entry['teacher_remarks'] ?? null,
                    'teacher_id' => $teacherId,
                ]
            );
            $savedCount++;
        }

        Notification::make()
            ->success()
            ->title($savedCount . ' জন ছাত্রের প্রগ্রেস সংরক্ষিত!')
            ->send();
    }
}
