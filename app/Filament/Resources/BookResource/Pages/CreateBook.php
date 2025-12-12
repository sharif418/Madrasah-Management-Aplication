<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure available_copies doesn't exceed total_copies
        if (($data['available_copies'] ?? 0) > ($data['total_copies'] ?? 1)) {
            $data['available_copies'] = $data['total_copies'];
        }
        return $data;
    }
}
