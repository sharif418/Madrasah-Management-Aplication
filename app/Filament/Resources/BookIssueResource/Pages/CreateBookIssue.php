<?php

namespace App\Filament\Resources\BookIssueResource\Pages;

use App\Filament\Resources\BookIssueResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\LibraryMember;

class CreateBookIssue extends CreateRecord
{
    protected static string $resource = BookIssueResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {
        $memberId = $this->data['library_member_id'] ?? null;

        if ($memberId) {
            $member = LibraryMember::find($memberId);

            if ($member && !$member->canBorrowMore()) {
                Notification::make()
                    ->danger()
                    ->title('ইস্যু করা যাবে না!')
                    ->body("এই সদস্য ইতিমধ্যে সর্বোচ্চ {$member->max_books}টি বই নিয়েছেন।")
                    ->send();

                $this->halt();
            }
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('বই ইস্যু সম্পন্ন!')
            ->body('বই সফলভাবে ইস্যু করা হয়েছে।');
    }
}
