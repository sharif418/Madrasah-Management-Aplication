<?php

namespace App\Filament\Resources\HifzProgressResource\Pages;

use App\Filament\Resources\HifzProgressResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHifzProgress extends EditRecord
{
    protected static string $resource = HifzProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
