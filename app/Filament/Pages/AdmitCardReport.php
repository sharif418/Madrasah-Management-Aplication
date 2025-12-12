<?php

namespace App\Filament\Pages;

use App\Models\ClassName;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Blade;

class AdmitCardReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'প্রবেশপত্র (Admit Card)';

    protected static ?string $title = 'প্রবেশপত্র রিপোর্ট';

    protected static ?string $navigationGroup = 'পরীক্ষা ব্যবস্থাপনা';

    protected static string $view = 'filament.pages.admit-card-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('exam_id')
                    ->label('পরীক্ষা')
                    ->options(Exam::latest()->take(10)->pluck('name', 'id'))
                    ->required(),
                Select::make('class_id')
                    ->label('ক্লাস')
                    ->options(ClassName::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn() => $this->data['section_id'] = null),
                Select::make('section_id')
                    ->label('শাখা')
                    ->options(fn(Get $get) => Section::where('class_id', $get('class_id'))->pluck('name', 'id')),
            ])
            ->statePath('data');
    }

    public function generate()
    {
        $this->form->validate();

        $examId = $this->data['exam_id'];
        $classId = $this->data['class_id'];
        $sectionId = $this->data['section_id'] ?? null;

        $students = Student::with(['class', 'section', 'academicYear'])
            ->where('class_id', $classId)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->where('status', 'active')
            ->orderBy('roll_no')
            ->get();

        if ($students->isEmpty()) {
            Notification::make()->title('কোনো ছাত্র পাওয়া যায়নি')->warning()->send();
            return;
        }

        $exam = Exam::with('examType', 'academicYear')->find($examId);

        $schedules = ExamSchedule::with('subject')
            ->where('exam_id', $examId)
            ->where('class_id', $classId)
            ->orderBy('exam_date')
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
            Notification::make()->title('এই ক্লাসের জন্য পরীক্ষার রুটিন পাওয়া যায়নি')->warning()->send();
            return;
        }

        $data = [
            'students' => $students,
            'exam' => $exam,
            'schedules' => $schedules,
            'institute' => [
                'name' => setting('site_title', 'মাদরাসা ম্যানেজমেন্ট সিস্টেম'),
                'address' => setting('address', 'ঢাকা, বাংলাদেশ'),
                'logo' => setting('site_logo'), // logic to handle logo path
            ],
            'generated_at' => now()->format('d M Y, h:i A'),
        ];

        $pdf = Pdf::loadView('pdf.admit-card', $data)
            ->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'admit-cards-' . $exam->name . '-' . now()->timestamp . '.pdf');
    }
}
