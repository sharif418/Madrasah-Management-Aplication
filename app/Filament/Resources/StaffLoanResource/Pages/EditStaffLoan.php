<?php

namespace App\Filament\Resources\StaffLoanResource\Pages;

use App\Filament\Resources\StaffLoanResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditStaffLoan extends EditRecord
{
    protected static string $resource = StaffLoanResource::class;

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
