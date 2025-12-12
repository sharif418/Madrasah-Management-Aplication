<?php

namespace App\Filament\Resources\EmergencyAlertResource\Pages;

use App\Filament\Resources\EmergencyAlertResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListEmergencyAlerts extends ListRecords
{
    protected static string $resource = EmergencyAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন জরুরি বার্তা'),
        ];
    }
}
