<?php

namespace App\Filament\Resources\IncomeHeadResource\Pages;

use App\Filament\Resources\IncomeHeadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIncomeHead extends CreateRecord
{
    protected static string $resource = IncomeHeadResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
