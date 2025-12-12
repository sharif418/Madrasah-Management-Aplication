<?php

namespace App\Filament\Resources\LibraryMemberResource\Pages;

use App\Filament\Resources\LibraryMemberResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLibraryMember extends CreateRecord
{
    protected static string $resource = LibraryMemberResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
