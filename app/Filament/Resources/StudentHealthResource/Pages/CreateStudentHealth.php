<?php
namespace App\Filament\Resources\StudentHealthResource\Pages;
use App\Filament\Resources\StudentHealthResource;
use Filament\Resources\Pages\CreateRecord;
class CreateStudentHealth extends CreateRecord
{
    protected static string $resource = StudentHealthResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
