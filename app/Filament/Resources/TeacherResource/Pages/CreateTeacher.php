<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    protected ?string $generatedPassword = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        $message = 'নতুন শিক্ষক সফলভাবে যোগ করা হয়েছে। কর্মচারী আইডি: ' . $this->record->employee_id;

        if ($this->generatedPassword) {
            $message .= "\n\n🔐 লগইন তথ্য:\nEmail: " . $this->record->email . "\nPassword: " . $this->generatedPassword;
        }

        return Notification::make()
            ->success()
            ->title('সফল!')
            ->body($message)
            ->persistent();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['employee_id'])) {
            $data['employee_id'] = \App\Models\Teacher::generateEmployeeId();
        }

        return $data;
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

                // Assign teacher role
                $user->assignRole('teacher');

                // Link user to teacher
                $this->record->update(['user_id' => $user->id]);

                // Show password in notification (one-time display)
                Notification::make()
                    ->warning()
                    ->title('🔐 লগইন তথ্য সংরক্ষণ করুন!')
                    ->body("Email: {$this->record->email}\nPassword: {$this->generatedPassword}\n\nAdmin Portal: /admin")
                    ->persistent()
                    ->send();
            } else {
                // Link existing user
                $this->record->update(['user_id' => $existingUser->id]);
            }
        }
    }
}
