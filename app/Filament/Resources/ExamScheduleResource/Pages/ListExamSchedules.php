<?php

namespace App\Filament\Resources\ExamScheduleResource\Pages;

use App\Filament\Resources\ExamScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ExamSchedule;
use App\Models\Exam;

class ListExamSchedules extends ListRecords
{
    protected static string $resource = ExamScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন সময়সূচী'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'সকল' => Tab::make()
                ->badge(fn() => ExamSchedule::count()),
        ];

        // Add tabs for recent exams
        $exams = Exam::where('status', '!=', 'completed')
            ->orderBy('start_date', 'desc')
            ->limit(3)
            ->get();

        foreach ($exams as $exam) {
            $tabs[$exam->name] = Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('exam_id', $exam->id))
                ->badge(fn() => ExamSchedule::where('exam_id', $exam->id)->count());
        }

        return $tabs;
    }
}
