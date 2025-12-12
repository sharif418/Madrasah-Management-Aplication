<?php

namespace App\Filament\Resources\IncomeHeadResource\Pages;

use App\Filament\Resources\IncomeHeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIncomeHeads extends ListRecords
{
    protected static string $resource = IncomeHeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন আয়ের খাত'),
        ];
    }
}
