<?php

namespace App\Filament\Resources\SmsLogResource\Pages;

use App\Filament\Resources\SmsLogResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateSmsLog extends CreateRecord
{
    protected static string $resource = SmsLogResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        // TODO: Integrate with SMS gateway here
        // For now, just mark as pending

        Notification::make()
            ->info()
            ->title('SMS কিউতে যোগ করা হয়েছে')
            ->body('SMS গেটওয়ে ইন্টিগ্রেশন প্রয়োজন')
            ->send();
    }
}
