<?php

namespace App\Filament\Resources\GuardianResource\Pages;

use App\Filament\Resources\GuardianResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateGuardian extends CreateRecord
{
    protected static string $resource = GuardianResource::class;

    protected ?string $generatedPassword = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        $message = 'নতুন অভিভাবক সফলভাবে যোগ করা হয়েছে।';

        if ($this->generatedPassword) {
            $message .= "\n\n🔐 লগইন তথ্য:\nEmail: " . $this->record->email . "\nPassword: " . $this->generatedPassword;
        }

        return Notification::make()
            ->success()
            ->title('সফল!')
            ->body($message)
            ->persistent();
    }

    protected function afterCreate(): void
    {
        // Create user account if email is provided and no user_id
        if (!empty($this->record->email) && empty($this->record->user_id)) {
            // Check if user already exists
            $existingUser = User::where('email', $this->record->email)->first();

            if (!$existingUser) {
                // Generate random password
                $this->generatedPassword = Str::random(8);

                // Create user
                $user = User::create([
                    'name' => $this->record->name,
                    'email' => $this->record->email,
                    'password' => Hash::make($this->generatedPassword),
                    'status' => 'active',
                ]);

                // Assign parent role
                $user->assignRole('parent');

                // Link user to guardian
                $this->record->update(['user_id' => $user->id]);

                // Show password in notification (one-time display)
                Notification::make()
                    ->warning()
                    ->title('🔐 লগইন তথ্য সংরক্ষণ করুন!')
                    ->body("Email: {$this->record->email}\nPassword: {$this->generatedPassword}\n\nParent Portal: /parent")
                    ->persistent()
                    ->send();
            } else {
                // Link existing user
                $this->record->update(['user_id' => $existingUser->id]);
            }
        }
    }
}
