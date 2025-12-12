<?php

namespace App\Filament\Resources\ExpenseHeadResource\Pages;

use App\Filament\Resources\ExpenseHeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpenseHeads extends ListRecords
{
    protected static string $resource = ExpenseHeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন ব্যয়ের খাত'),
        ];
    }
}
