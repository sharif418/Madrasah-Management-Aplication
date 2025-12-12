<?php

namespace App\Filament\Resources\KitabProgressResource\Pages;

use App\Filament\Resources\KitabProgressResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKitabProgress extends CreateRecord
{
    protected static string $resource = KitabProgressResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
