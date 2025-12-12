<?php

namespace App\Filament\Resources\FeeWaiverResource\Pages;

use App\Filament\Resources\FeeWaiverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeeWaivers extends ListRecords
{
    protected static string $resource = FeeWaiverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('নতুন মওকুফ আবেদন')
                ->icon('heroicon-o-plus'),
        ];
    }
}
