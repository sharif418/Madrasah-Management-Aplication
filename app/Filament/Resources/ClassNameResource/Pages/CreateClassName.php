<?php

namespace App\Filament\Resources\ClassNameResource\Pages;

use App\Filament\Resources\ClassNameResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateClassName extends CreateRecord
{
    protected static string $resource = ClassNameResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('সফল!')
            ->body('নতুন শ্রেণি সফলভাবে তৈরি হয়েছে।');
    }
}
