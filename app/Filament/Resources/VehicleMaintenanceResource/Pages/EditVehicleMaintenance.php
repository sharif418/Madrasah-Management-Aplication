<?php
namespace App\Filament\Resources\VehicleMaintenanceResource\Pages;
use App\Filament\Resources\VehicleMaintenanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditVehicleMaintenance extends EditRecord
{
    protected static string $resource = VehicleMaintenanceResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
