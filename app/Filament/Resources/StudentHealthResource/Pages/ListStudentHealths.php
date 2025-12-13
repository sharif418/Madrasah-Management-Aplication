<?php
namespace App\Filament\Resources\StudentHealthResource\Pages;
use App\Filament\Resources\StudentHealthResource;
use Filament\Resources\Pages\ListRecords;
class ListStudentHealths extends ListRecords
{
    protected static string $resource = StudentHealthResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()->label('নতুন স্বাস্থ্য প্রোফাইল')];
    }
}
