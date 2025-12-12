<?php

namespace App\Filament\Resources\ExpenseHeadResource\Pages;

use App\Filament\Resources\ExpenseHeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpenseHead extends EditRecord
{
    protected static string $resource = ExpenseHeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
