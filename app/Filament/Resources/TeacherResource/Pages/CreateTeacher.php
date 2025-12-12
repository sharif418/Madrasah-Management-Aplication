<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('সফল!')
            ->body('নতুন শিক্ষক সফলভাবে যোগ করা হয়েছে। কর্মচারী আইডি: ' . $this->record->employee_id);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['employee_id'])) {
            $data['employee_id'] = \App\Models\Teacher::generateEmployeeId();
        }

        return $data;
    }
}
