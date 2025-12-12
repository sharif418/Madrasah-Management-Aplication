<?php

namespace App\Filament\Student\Pages;

use App\Models\Student;
use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Support\Facades\Auth;

class MyProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static string $view = 'filament.student.pages.my-profile';

    protected static ?string $title = 'আমার প্রোফাইল';

    protected static ?string $navigationLabel = 'প্রোফাইল';

    protected static ?int $navigationSort = 2;

    public ?Student $student = null;

    public function mount(): void
    {
        $this->student = Auth::user()->student;
    }

    public function getStudentData(): array
    {
        if (!$this->student) {
            return [];
        }

        return [
            'name' => $this->student->name,
            'name_en' => $this->student->name_en,
            'admission_no' => $this->student->admission_no,
            'roll_no' => $this->student->roll_no,
            'class' => $this->student->class?->name,
            'section' => $this->student->section?->name,
            'father_name' => $this->student->father_name,
            'mother_name' => $this->student->mother_name,
            'date_of_birth' => $this->student->date_of_birth?->format('d M, Y'),
            'gender' => $this->student->gender === 'male' ? 'ছেলে' : 'মেয়ে',
            'blood_group' => $this->student->blood_group,
            'phone' => $this->student->phone,
            'email' => $this->student->email,
            'present_address' => $this->student->present_address,
            'admission_date' => $this->student->admission_date?->format('d M, Y'),
            'photo_url' => $this->student->photo_url,
        ];
    }
}
