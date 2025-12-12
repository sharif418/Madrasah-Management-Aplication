<?php

namespace App\Filament\Parent\Pages;

use App\Models\Student;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MyChildren extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'আমার সন্তান';

    protected static ?string $title = 'আমার সন্তানদের তথ্য';

    protected static string $view = 'filament.parent.pages.my-children';

    protected static ?int $navigationSort = 2;

    public ?int $selectedChildId = null;

    public function mount(): void
    {
        $children = $this->getChildren();
        if ($children->isNotEmpty()) {
            $this->selectedChildId = $children->first()->id;
        }
    }

    public function getChildren()
    {
        $guardian = Auth::user()->guardian;

        if (!$guardian) {
            return collect();
        }

        return Student::with(['class', 'section', 'academicYear', 'guardian'])
            ->where('guardian_id', $guardian->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function selectChild(int $childId): void
    {
        $this->selectedChildId = $childId;
    }

    public function getSelectedChild()
    {
        if (!$this->selectedChildId) {
            return null;
        }

        return Student::with(['class', 'section', 'academicYear', 'guardian'])
            ->find($this->selectedChildId);
    }
}
