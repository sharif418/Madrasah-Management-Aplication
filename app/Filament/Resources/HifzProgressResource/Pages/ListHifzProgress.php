<?php

namespace App\Filament\Resources\HifzProgressResource\Pages;

use App\Filament\Resources\HifzProgressResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHifzProgress extends ListRecords
{
    protected static string $resource = HifzProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন এন্ট্রি'),
        ];
    }
}
