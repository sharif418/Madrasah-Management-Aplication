<?php

namespace App\Filament\Resources\StaffLoanResource\Pages;

use App\Filament\Resources\StaffLoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaffLoan extends CreateRecord
{
    protected static string $resource = StaffLoanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
