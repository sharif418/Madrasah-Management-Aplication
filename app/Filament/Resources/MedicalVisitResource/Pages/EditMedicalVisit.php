<?php
namespace App\Filament\Resources\MedicalVisitResource\Pages;
use App\Filament\Resources\MedicalVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditMedicalVisit extends EditRecord
{
    protected static string $resource = MedicalVisitResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
