<?php

namespace App\Filament\Resources\NoticeResource\Pages;

use App\Filament\Resources\NoticeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Notice;

class ListNotices extends ListRecords
{
    protected static string $resource = NoticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন নোটিশ'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'সকল' => Tab::make()
                ->badge(fn() => Notice::count()),

            'জরুরি' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'urgent'))
                ->badge(fn() => Notice::where('type', 'urgent')->count())
                ->badgeColor('danger'),

            'প্রকাশিত' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_published', true))
                ->badge(fn() => Notice::where('is_published', true)->count())
                ->badgeColor('success'),
        ];
    }
}
