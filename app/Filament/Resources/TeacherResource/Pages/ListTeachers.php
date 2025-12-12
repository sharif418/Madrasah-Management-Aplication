<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTeachers extends ListRecords
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('নতুন শিক্ষক')
                ->icon('heroicon-o-plus'),
            Actions\Action::make('import')
                ->label('ইমপোর্ট')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray'),
            Actions\Action::make('export')
                ->label('এক্সপোর্ট')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'সকল' => Tab::make()
                ->badge(fn() => $this->getModel()::count()),
            'সক্রিয়' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active'))
                ->badge(fn() => $this->getModel()::where('status', 'active')->count())
                ->badgeColor('success'),
            'ছুটিতে' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'on_leave'))
                ->badge(fn() => $this->getModel()::where('status', 'on_leave')->count())
                ->badgeColor('warning'),
            'স্থায়ী' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('employment_type', 'permanent')->where('status', 'active'))
                ->badge(fn() => $this->getModel()::where('employment_type', 'permanent')->where('status', 'active')->count())
                ->badgeColor('info'),
            'পদত্যাগ/অবসর' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('status', ['resigned', 'retired', 'terminated']))
                ->badge(fn() => $this->getModel()::whereIn('status', ['resigned', 'retired', 'terminated'])->count())
                ->badgeColor('gray'),
        ];
    }
}
