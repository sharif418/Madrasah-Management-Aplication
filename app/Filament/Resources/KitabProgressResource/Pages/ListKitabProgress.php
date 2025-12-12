<?php

namespace App\Filament\Resources\KitabProgressResource\Pages;

use App\Filament\Resources\KitabProgressResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKitabProgress extends ListRecords
{
    protected static string $resource = KitabProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন এন্ট্রি'),
        ];
    }
}
