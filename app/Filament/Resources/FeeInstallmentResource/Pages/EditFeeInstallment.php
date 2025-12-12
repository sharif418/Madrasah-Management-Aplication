<?php

namespace App\Filament\Resources\FeeInstallmentResource\Pages;

use App\Filament\Resources\FeeInstallmentResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditFeeInstallment extends EditRecord
{
    protected static string $resource = FeeInstallmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
