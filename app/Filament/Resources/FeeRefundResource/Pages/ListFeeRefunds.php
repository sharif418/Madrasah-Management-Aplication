<?php

namespace App\Filament\Resources\FeeRefundResource\Pages;

use App\Filament\Resources\FeeRefundResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListFeeRefunds extends ListRecords
{
    protected static string $resource = FeeRefundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('নতুন ফেরত আবেদন'),
        ];
    }
}
