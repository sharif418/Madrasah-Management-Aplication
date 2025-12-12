<?php

namespace App\Filament\Resources\HifzSummaryResource\Pages;

use App\Filament\Resources\HifzSummaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\HifzSummary;

class ListHifzSummaries extends ListRecords
{
    protected static string $resource = HifzSummaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন এন্ট্রি'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'সকল' => Tab::make()
                ->badge(fn() => HifzSummary::count()),

            'চলমান' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'ongoing'))
                ->badge(fn() => HifzSummary::where('status', 'ongoing')->count())
                ->badgeColor('info'),

            'হাফেজ' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_hafiz', true))
                ->badge(fn() => HifzSummary::where('is_hafiz', true)->count())
                ->badgeColor('success'),
        ];
    }
}
