<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('সম্পাদনা'),
            Actions\Action::make('idCard')
                ->label('আইডি কার্ড')
                ->icon('heroicon-o-identification')
                ->url(fn() => route('student.id-card', $this->record))
                ->openUrlInNewTab()
                ->color('info'),
            Actions\Action::make('tc')
                ->label('টিসি/ছাড়পত্র')
                ->icon('heroicon-o-document-text')
                ->url(fn() => route('student.tc', $this->record))
                ->openUrlInNewTab()
                ->color('warning')
                ->visible(fn() => $this->record->status !== 'active'),
            Actions\DeleteAction::make()
                ->label('মুছে ফেলুন'),
        ];
    }
}
