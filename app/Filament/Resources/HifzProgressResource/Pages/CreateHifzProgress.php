<?php

namespace App\Filament\Resources\HifzProgressResource\Pages;

use App\Filament\Resources\HifzProgressResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHifzProgress extends CreateRecord
{
    protected static string $resource = HifzProgressResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
