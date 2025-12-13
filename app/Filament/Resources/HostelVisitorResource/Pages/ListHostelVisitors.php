<?php
namespace App\Filament\Resources\HostelVisitorResource\Pages;
use App\Filament\Resources\HostelVisitorResource;
use Filament\Resources\Pages\ListRecords;
class ListHostelVisitors extends ListRecords
{
    protected static string $resource = HostelVisitorResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()->label('নতুন ভিজিটর')];
    }
}
