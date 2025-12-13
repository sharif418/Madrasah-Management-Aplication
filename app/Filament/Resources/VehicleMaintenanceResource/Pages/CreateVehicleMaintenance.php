<?php
namespace App\Filament\Resources\VehicleMaintenanceResource\Pages;
use App\Filament\Resources\VehicleMaintenanceResource;
use Filament\Resources\Pages\CreateRecord;
class CreateVehicleMaintenance extends CreateRecord
{
    protected static string $resource = VehicleMaintenanceResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
