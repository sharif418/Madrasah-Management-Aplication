<?php

namespace App\Filament\Resources\HifzSummaryResource\Pages;

use App\Filament\Resources\HifzSummaryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHifzSummary extends CreateRecord
{
    protected static string $resource = HifzSummaryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
