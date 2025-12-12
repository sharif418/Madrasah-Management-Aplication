<?php

namespace App\Filament\Resources\SalaryAdvanceResource\Pages;

use App\Filament\Resources\SalaryAdvanceResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditSalaryAdvance extends EditRecord
{
    protected static string $resource = SalaryAdvanceResource::class;

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
