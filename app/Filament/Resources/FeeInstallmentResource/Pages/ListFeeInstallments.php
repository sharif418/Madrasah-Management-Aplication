<?php

namespace App\Filament\Resources\FeeInstallmentResource\Pages;

use App\Filament\Resources\FeeInstallmentResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListFeeInstallments extends ListRecords
{
    protected static string $resource = FeeInstallmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('নতুন কিস্তি'),
        ];
    }
}
