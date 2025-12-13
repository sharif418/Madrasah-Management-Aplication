<?php
namespace App\Filament\Resources\FuelLogResource\Pages;
use App\Filament\Resources\FuelLogResource;
use Filament\Resources\Pages\ListRecords;
class ListFuelLogs extends ListRecords
{
    protected static string $resource = FuelLogResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()->label('নতুন জ্বালানি এন্ট্রি')];
    }
}
