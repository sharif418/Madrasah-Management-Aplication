<?php
namespace App\Filament\Resources\HostelVisitorResource\Pages;
use App\Filament\Resources\HostelVisitorResource;
use Filament\Resources\Pages\CreateRecord;
class CreateHostelVisitor extends CreateRecord
{
    protected static string $resource = HostelVisitorResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
