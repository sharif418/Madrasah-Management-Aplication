<?php

namespace App\Filament\Resources\EmergencyAlertResource\Pages;

use App\Filament\Resources\EmergencyAlertResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmergencyAlert extends CreateRecord
{
    protected static string $resource = EmergencyAlertResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
