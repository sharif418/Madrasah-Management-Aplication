<?php
namespace App\Filament\Resources\MedicalVisitResource\Pages;
use App\Filament\Resources\MedicalVisitResource;
use Filament\Resources\Pages\CreateRecord;
class CreateMedicalVisit extends CreateRecord
{
    protected static string $resource = MedicalVisitResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
