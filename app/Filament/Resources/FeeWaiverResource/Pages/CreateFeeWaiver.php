<?php

namespace App\Filament\Resources\FeeWaiverResource\Pages;

use App\Filament\Resources\FeeWaiverResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeeWaiver extends CreateRecord
{
    protected static string $resource = FeeWaiverResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'ফি মওকুফ আবেদন জমা হয়েছে!';
    }
}
