<?php

namespace App\Filament\Resources\TransportRouteResource\Pages;

use App\Filament\Resources\TransportRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransportRoutes extends ListRecords
{
    protected static string $resource = TransportRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন রুট'),
        ];
    }
}
