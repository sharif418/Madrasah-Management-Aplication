<?php

namespace App\Filament\Resources\HostelAllocationResource\Pages;

use App\Filament\Resources\HostelAllocationResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\HostelRoom;

class CreateHostelAllocation extends CreateRecord
{
    protected static string $resource = HostelAllocationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {
        $roomId = $this->data['hostel_room_id'] ?? null;

        if ($roomId) {
            $room = HostelRoom::find($roomId);

            if ($room && !$room->hasSpace()) {
                Notification::make()
                    ->danger()
                    ->title('বরাদ্দ করা যাবে না!')
                    ->body('এই রুমে কোন খালি সিট নেই।')
                    ->send();

                $this->halt();
            }
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('সিট বরাদ্দ সম্পন্ন!');
    }
}
