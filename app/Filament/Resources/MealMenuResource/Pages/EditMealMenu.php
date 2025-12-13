<?php
namespace App\Filament\Resources\MealMenuResource\Pages;
use App\Filament\Resources\MealMenuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditMealMenu extends EditRecord
{
    protected static string $resource = MealMenuResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
