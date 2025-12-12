<?php

namespace App\Filament\Resources\KitabResource\Pages;

use App\Filament\Resources\KitabResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKitabs extends ListRecords
{
    protected static string $resource = KitabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন কিতাব'),
        ];
    }
}
