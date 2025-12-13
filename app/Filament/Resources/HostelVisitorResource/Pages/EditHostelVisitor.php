<?php
namespace App\Filament\Resources\HostelVisitorResource\Pages;
use App\Filament\Resources\HostelVisitorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditHostelVisitor extends EditRecord
{
    protected static string $resource = HostelVisitorResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
