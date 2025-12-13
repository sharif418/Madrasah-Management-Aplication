<?php
namespace App\Filament\Resources\DisciplineIncidentResource\Pages;
use App\Filament\Resources\DisciplineIncidentResource;
use Filament\Resources\Pages\ListRecords;
class ListDisciplineIncidents extends ListRecords
{
    protected static string $resource = DisciplineIncidentResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()->label('নতুন ঘটনা রেকর্ড')];
    }
}
