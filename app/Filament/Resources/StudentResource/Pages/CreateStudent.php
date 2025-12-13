<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected ?string $generatedPassword = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        $message = 'নতুন ছাত্র সফলভাবে ভর্তি করা হয়েছে। ভর্তি নম্বর: ' . $this->record->admission_no;

        if ($this->generatedPassword) {
            $message .= "\n\n🔐 লগইন তথ্য:\nEmail: " . $this->record->email . "\nPassword: " . $this->generatedPassword;
        }

        return Notification::make()
            ->success()
            ->title('ভর্তি সম্পন্ন!')
            ->body($message)
            ->persistent();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate admission number if not set
        if (empty($data['admission_no'])) {
            $data['admission_no'] = \App\Models\Student::generateAdmissionNo();
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

                // Assign student role
                $user->assignRole('student');

                // Link user to student
                $this->record->update(['user_id' => $user->id]);

                // Show password in notification (one-time display)
                Notification::make()
                    ->warning()
                    ->title('🔐 লগইন তথ্য সংরক্ষণ করুন!')
                    ->body("Email: {$this->record->email}\nPassword: {$this->generatedPassword}\n\nStudent Portal: /student")
                    ->persistent()
                    ->send();
            } else {
                // Link existing user
                $this->record->update(['user_id' => $existingUser->id]);
            }
        }
    }
}
