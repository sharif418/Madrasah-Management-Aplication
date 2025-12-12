<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\ExamResult;
use App\Models\Grade;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Actions;
use Illuminate\Support\Collection;

class ExamResults extends Page
{
    protected static string $resource = ExamResource::class;

    protected static string $view = 'filament.resources.exam-resource.pages.exam-results';

    protected static ?string $title = 'পরীক্ষার ফলাফল';

    public $record;
    public Collection $results;
    public Collection $subjects;
    public array $studentMarks = [];

    public function mount($record): void
    {
        $this->record = Exam::with(['class', 'schedules.subject'])->findOrFail($record);
        $this->subjects = $this->record->schedules->pluck('subject');
        $this->loadResults();
    }

    public function loadResults(): void
    {
        // Get all marks for this exam
        $marks = Mark::where('exam_id', $this->record->id)
            ->with(['student', 'subject', 'grade'])
            ->get()
            ->groupBy('student_id');

        $this->studentMarks = [];

        foreach ($marks as $studentId => $studentMarks) {
            $student = $studentMarks->first()->student;
            $totalObtained = 0;
            $totalFull = 0;
            $subjectMarks = [];
            $allPassed = true;

            foreach ($studentMarks as $mark) {
                $totalObtained += $mark->marks_obtained;
                $totalFull += $mark->full_marks;
                $subjectMarks[$mark->subject_id] = [
                    'marks' => $mark->marks_obtained,
                    'full' => $mark->full_marks,
                    'pass' => $mark->pass_marks,
                    'passed' => $mark->is_passed,
                    'grade' => $mark->grade?->name,
                ];

                if (!$mark->is_passed) {
                    $allPassed = false;
                }
            }

            $percentage = $totalFull > 0 ? ($totalObtained / $totalFull) * 100 : 0;
            $grade = Grade::getGradeForMarks($percentage);

            $this->studentMarks[$studentId] = [
                'student' => $student,
                'subjects' => $subjectMarks,
                'total_obtained' => $totalObtained,
                'total_full' => $totalFull,
                'percentage' => round($percentage, 2),
                'grade' => $grade?->name ?? '-',
                'gpa' => $grade?->grade_point ?? 0,
                'is_passed' => $allPassed,
            ];
        }

        // Sort by percentage descending for position
        uasort($this->studentMarks, fn($a, $b) => $b['percentage'] <=> $a['percentage']);

        // Assign positions
        $position = 1;
        foreach ($this->studentMarks as &$data) {
            $data['position'] = $position++;
        }
    }

    public function processResults(): void
    {
        foreach ($this->studentMarks as $studentId => $data) {
            $student = $data['student'];
            $grade = Grade::getGradeForMarks($data['percentage']);

            ExamResult::updateOrCreate(
                [
                    'exam_id' => $this->record->id,
                    'student_id' => $studentId,
                ],
                [
                    'class_id' => $student->class_id ?? $this->record->class_id,
                    'total_marks' => $data['total_obtained'],
                    'total_full_marks' => $data['total_full'],
                    'percentage' => $data['percentage'],
                    'gpa' => $data['gpa'],
                    'grade' => $grade?->name,
                    'position' => $data['position'],
                    'result_status' => $data['is_passed'] ? 'pass' : 'fail',
                ]
            );
        }

        // Update exam status
        $this->record->update(['status' => 'result_published', 'is_published' => true]);

        Notification::make()
            ->success()
            ->title('ফলাফল প্রক্রিয়াকরণ সম্পন্ন!')
            ->body('সকল ফলাফল সংরক্ষণ করা হয়েছে এবং প্রকাশিত হয়েছে।')
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('পরীক্ষার তালিকায়')
                ->icon('heroicon-o-arrow-left')
                ->url(ExamResource::getUrl('index')),
            Actions\Action::make('process')
                ->label('ফলাফল প্রক্রিয়াকরণ ও প্রকাশ')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action('processResults')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->status !== 'result_published'),
            Actions\Action::make('print')
                ->label('প্রিন্ট/PDF')
                ->icon('heroicon-o-printer')
                ->url(fn() => route('exam.tabulation', $this->record))
                ->openUrlInNewTab()
                ->color('gray'),
        ];
    }
}
