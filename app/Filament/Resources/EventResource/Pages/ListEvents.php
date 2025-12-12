<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Event;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন ইভেন্ট'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'সকল' => Tab::make()
                ->badge(fn() => Event::count()),

            'আসন্ন' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('start_date', '>=', now()->toDateString()))
                ->badge(fn() => Event::where('start_date', '>=', now()->toDateString())->count())
                ->badgeColor('info'),

            'ছুটি' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_holiday', true))
                ->badge(fn() => Event::where('is_holiday', true)->count())
                ->badgeColor('warning'),
        ];
    }
}
