<?php

namespace App\Filament\Resources\TransportAllocationResource\Pages;

use App\Filament\Resources\TransportAllocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransportAllocation extends EditRecord
{
    protected static string $resource = TransportAllocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
