<?php

namespace App\Filament\Parent\Pages;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Mark;
use App\Models\Student;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ResultsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'পরীক্ষার ফলাফল';

    protected static ?string $title = 'পরীক্ষার ফলাফল';

    protected static ?string $slug = 'results';

    protected static string $view = 'filament.parent.pages.results';

    protected static ?int $navigationSort = 4;

    public ?array $data = [];

    public function mount(): void
    {
        $children = $this->getChildren();
        $this->form->fill([
            'student_id' => $children->first()?->id,
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
                Select::make('exam_id')
                    ->label('পরীক্ষা')
                    ->options(fn() => $this->getExams()->pluck('name', 'id'))
                    ->native(false)
                    ->live(),
            ])
            ->columns(2)
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

    public function getExams()
    {
        return Exam::where('is_published', true)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function getResults(): array
    {
        $studentId = $this->data['student_id'] ?? null;
        $examId = $this->data['exam_id'] ?? null;

        if (!$studentId) {
            return ['results' => [], 'marks' => []];
        }

        // Get all results if no specific exam selected
        $resultsQuery = ExamResult::with(['exam.examType', 'exam.academicYear'])
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc');

        if ($examId) {
            $resultsQuery->where('exam_id', $examId);
        }

        $results = $resultsQuery->get();

        // Get marks for selected exam
        $marks = [];
        if ($examId) {
            $marks = Mark::with('subject')
                ->where('student_id', $studentId)
                ->where('exam_id', $examId)
                ->get();
        }

        return [
            'results' => $results,
            'marks' => $marks,
        ];
    }
}
