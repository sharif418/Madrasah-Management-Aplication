<?php

namespace App\Filament\Resources\IncomeHeadResource\Pages;

use App\Filament\Resources\IncomeHeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIncomeHead extends EditRecord
{
    protected static string $resource = IncomeHeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
