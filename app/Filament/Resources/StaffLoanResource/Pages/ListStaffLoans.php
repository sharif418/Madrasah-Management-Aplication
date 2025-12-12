<?php

namespace App\Filament\Resources\StaffLoanResource\Pages;

use App\Filament\Resources\StaffLoanResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListStaffLoans extends ListRecords
{
    protected static string $resource = StaffLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন ঋণ'),
        ];
    }
}
