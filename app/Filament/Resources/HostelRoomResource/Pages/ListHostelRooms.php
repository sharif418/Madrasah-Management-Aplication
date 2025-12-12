<?php

namespace App\Filament\Resources\HostelRoomResource\Pages;

use App\Filament\Resources\HostelRoomResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHostelRooms extends ListRecords
{
    protected static string $resource = HostelRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন রুম'),
        ];
    }
}
