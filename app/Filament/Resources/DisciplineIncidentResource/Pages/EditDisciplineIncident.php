<?php
namespace App\Filament\Resources\DisciplineIncidentResource\Pages;
use App\Filament\Resources\DisciplineIncidentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditDisciplineIncident extends EditRecord
{
    protected static string $resource = DisciplineIncidentResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('notifyParent')
                ->label('অভিভাবক জানান')
                ->icon('heroicon-o-phone')
                ->color('warning')
                ->action(fn() => $this->record->update([
                    'parent_notified' => true,
                    'parent_notified_date' => now(),
                ]))
                ->visible(fn() => !$this->record->parent_notified)
                ->requiresConfirmation(),
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
