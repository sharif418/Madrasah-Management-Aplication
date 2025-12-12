<?php

namespace App\Filament\Resources\ClassNameResource\Pages;

use App\Filament\Resources\ClassNameResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewClassName extends ViewRecord
{
    protected static string $resource = ClassNameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('সম্পাদনা'),
            Actions\Action::make('sections')
                ->label('শাখা পরিচালনা')
                ->icon('heroicon-o-squares-plus')
                ->url(fn() => ClassNameResource::getUrl('sections', ['record' => $this->record])),
        ];
    }
}
