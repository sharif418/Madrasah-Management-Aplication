<?php

namespace App\Filament\Resources\SyllabusResource\Pages;

use App\Filament\Resources\SyllabusResource;
use Filament\Resources\Pages\ListRecords;

class ListSyllabi extends ListRecords
{
    protected static string $resource = SyllabusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->label('নতুন সিলেবাস'),
        ];
    }
}
