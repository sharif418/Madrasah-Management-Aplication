<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListExams extends ListRecords
{
    protected static string $resource = ExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('নতুন পরীক্ষা')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'সকল' => Tab::make()
                ->badge(fn() => $this->getModel()::count()),
            'আসন্ন' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'upcoming'))
                ->badge(fn() => $this->getModel()::where('status', 'upcoming')->count())
                ->badgeColor('warning'),
            'চলমান' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'ongoing'))
                ->badge(fn() => $this->getModel()::where('status', 'ongoing')->count())
                ->badgeColor('info'),
            'সম্পন্ন' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                ->badge(fn() => $this->getModel()::where('status', 'completed')->count())
                ->badgeColor('success'),
            'ফলাফল প্রকাশিত' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'result_published'))
                ->badge(fn() => $this->getModel()::where('status', 'result_published')->count())
                ->badgeColor('primary'),
        ];
    }
}
