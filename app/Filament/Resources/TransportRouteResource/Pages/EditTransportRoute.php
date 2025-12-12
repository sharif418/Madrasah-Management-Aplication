<?php

namespace App\Filament\Resources\TransportRouteResource\Pages;

use App\Filament\Resources\TransportRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransportRoute extends EditRecord
{
    protected static string $resource = TransportRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
