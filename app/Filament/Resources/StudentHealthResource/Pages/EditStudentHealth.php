<?php
namespace App\Filament\Resources\StudentHealthResource\Pages;
use App\Filament\Resources\StudentHealthResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditStudentHealth extends EditRecord
{
    protected static string $resource = StudentHealthResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
