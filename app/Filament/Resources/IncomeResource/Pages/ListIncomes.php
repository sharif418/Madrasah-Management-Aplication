<?php

namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Resources\IncomeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Income;

class ListIncomes extends ListRecords
{
    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন আয় এন্ট্রি'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Could add stats widget here
        ];
    }
}
