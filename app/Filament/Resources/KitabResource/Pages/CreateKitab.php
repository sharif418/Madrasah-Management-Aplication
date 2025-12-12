<?php

namespace App\Filament\Resources\KitabResource\Pages;

use App\Filament\Resources\KitabResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKitab extends CreateRecord
{
    protected static string $resource = KitabResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
