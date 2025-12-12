<?php

namespace App\Filament\Resources\AdmissionApplicationResource\Pages;

use App\Filament\Resources\AdmissionApplicationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdmissionApplication extends CreateRecord
{
    protected static string $resource = AdmissionApplicationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
