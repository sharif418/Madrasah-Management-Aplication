<?php

namespace App\Filament\Resources\FeeDiscountResource\Pages;

use App\Filament\Resources\FeeDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeeDiscount extends EditRecord
{
    protected static string $resource = FeeDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('মুছুন'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'ফি ছাড় আপডেট হয়েছে!';
    }
}
