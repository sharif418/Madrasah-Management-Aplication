<?php

namespace App\Filament\Resources\LibraryMemberResource\Pages;

use App\Filament\Resources\LibraryMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLibraryMember extends EditRecord
{
    protected static string $resource = LibraryMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
