<?php

namespace App\Filament\Resources\TransportAllocationResource\Pages;

use App\Filament\Resources\TransportAllocationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransportAllocation extends CreateRecord
{
    protected static string $resource = TransportAllocationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
