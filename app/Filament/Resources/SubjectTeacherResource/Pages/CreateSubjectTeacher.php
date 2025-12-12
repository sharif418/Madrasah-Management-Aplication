<?php

namespace App\Filament\Resources\SubjectTeacherResource\Pages;

use App\Filament\Resources\SubjectTeacherResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubjectTeacher extends CreateRecord
{
    protected static string $resource = SubjectTeacherResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
