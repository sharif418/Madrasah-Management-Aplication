<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('ভর্তি সম্পন্ন!')
            ->body('নতুন ছাত্র সফলভাবে ভর্তি করা হয়েছে। ভর্তি নম্বর: ' . $this->record->admission_no);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate admission number if not set
        if (empty($data['admission_no'])) {
            $data['admission_no'] = \App\Models\Student::generateAdmissionNo();
        }

        return $data;
    }
}
