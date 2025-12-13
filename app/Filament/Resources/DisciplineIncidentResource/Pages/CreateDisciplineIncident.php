<?php
namespace App\Filament\Resources\DisciplineIncidentResource\Pages;
use App\Filament\Resources\DisciplineIncidentResource;
use Filament\Resources\Pages\CreateRecord;
class CreateDisciplineIncident extends CreateRecord
{
    protected static string $resource = DisciplineIncidentResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
