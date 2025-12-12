<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewExam extends ViewRecord
{
    protected static string $resource = ExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('সম্পাদনা'),
            Actions\Action::make('schedule')
                ->label('সূচি')
                ->icon('heroicon-o-calendar')
                ->url(fn() => ExamResource::getUrl('schedules', ['record' => $this->record])),
            Actions\Action::make('marks')
                ->label('নম্বর এন্ট্রি')
                ->icon('heroicon-o-pencil-square')
                ->url(fn() => ExamResource::getUrl('marks-entry', ['record' => $this->record]))
                ->visible(fn() => in_array($this->record->status, ['ongoing', 'completed'])),
        ];
    }
}
