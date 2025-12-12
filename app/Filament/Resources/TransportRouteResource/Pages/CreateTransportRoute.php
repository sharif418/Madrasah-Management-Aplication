<?php

namespace App\Filament\Resources\TransportRouteResource\Pages;

use App\Filament\Resources\TransportRouteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransportRoute extends CreateRecord
{
    protected static string $resource = TransportRouteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
