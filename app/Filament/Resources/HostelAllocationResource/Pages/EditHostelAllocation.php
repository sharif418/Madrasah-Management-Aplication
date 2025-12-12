<?php

namespace App\Filament\Resources\HostelAllocationResource\Pages;

use App\Filament\Resources\HostelAllocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHostelAllocation extends EditRecord
{
    protected static string $resource = HostelAllocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
