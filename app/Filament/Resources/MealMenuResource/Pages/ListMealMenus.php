<?php
namespace App\Filament\Resources\MealMenuResource\Pages;
use App\Filament\Resources\MealMenuResource;
use Filament\Resources\Pages\ListRecords;
class ListMealMenus extends ListRecords
{
    protected static string $resource = MealMenuResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()->label('নতুন মেনু')];
    }
}
