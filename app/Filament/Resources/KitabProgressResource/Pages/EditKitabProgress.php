<?php

namespace App\Filament\Resources\KitabProgressResource\Pages;

use App\Filament\Resources\KitabProgressResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKitabProgress extends EditRecord
{
    protected static string $resource = KitabProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
