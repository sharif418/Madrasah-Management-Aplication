<?php

namespace App\Filament\Resources\KitabResource\Pages;

use App\Filament\Resources\KitabResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKitab extends EditRecord
{
    protected static string $resource = KitabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
