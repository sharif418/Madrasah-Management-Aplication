<?php

namespace App\Filament\Resources\LibraryMemberResource\Pages;

use App\Filament\Resources\LibraryMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibraryMembers extends ListRecords
{
    protected static string $resource = LibraryMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন সদস্য'),
        ];
    }
}
