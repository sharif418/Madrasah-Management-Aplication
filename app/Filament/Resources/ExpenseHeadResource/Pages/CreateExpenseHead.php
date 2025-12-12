<?php

namespace App\Filament\Resources\ExpenseHeadResource\Pages;

use App\Filament\Resources\ExpenseHeadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExpenseHead extends CreateRecord
{
    protected static string $resource = ExpenseHeadResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
