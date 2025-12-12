<?php

namespace App\Filament\Resources\FeeDiscountResource\Pages;

use App\Filament\Resources\FeeDiscountResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeeDiscount extends CreateRecord
{
    protected static string $resource = FeeDiscountResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'ফি ছাড় সফলভাবে তৈরি হয়েছে!';
    }
}
