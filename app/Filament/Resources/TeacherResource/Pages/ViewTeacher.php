<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTeacher extends ViewRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('সম্পাদনা'),
            Actions\Action::make('idCard')
                ->label('আইডি কার্ড')
                ->icon('heroicon-o-identification')
                ->url(fn() => route('teacher.id-card', $this->record))
                ->openUrlInNewTab()
                ->color('info'),
            Actions\DeleteAction::make()
                ->label('মুছে ফেলুন'),
        ];
    }
}
