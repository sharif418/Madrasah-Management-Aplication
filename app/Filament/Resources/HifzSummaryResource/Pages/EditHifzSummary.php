<?php

namespace App\Filament\Resources\HifzSummaryResource\Pages;

use App\Filament\Resources\HifzSummaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHifzSummary extends EditRecord
{
    protected static string $resource = HifzSummaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Check if completed 30 para
        $this->record->checkCompletion();
    }
}
