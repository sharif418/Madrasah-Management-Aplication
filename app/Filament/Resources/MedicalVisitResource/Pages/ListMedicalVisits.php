<?php
namespace App\Filament\Resources\MedicalVisitResource\Pages;
use App\Filament\Resources\MedicalVisitResource;
use Filament\Resources\Pages\ListRecords;
class ListMedicalVisits extends ListRecords
{
    protected static string $resource = MedicalVisitResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()->label('নতুন ভিজিট রেকর্ড')];
    }
}
