<?php
namespace App\Filament\Resources\MealMenuResource\Pages;
use App\Filament\Resources\MealMenuResource;
use Filament\Resources\Pages\CreateRecord;
class CreateMealMenu extends CreateRecord
{
    protected static string $resource = MealMenuResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
