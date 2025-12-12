<?php

namespace App\Filament\Resources\FeeInstallmentResource\Pages;

use App\Filament\Resources\FeeInstallmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeeInstallment extends CreateRecord
{
    protected static string $resource = FeeInstallmentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
