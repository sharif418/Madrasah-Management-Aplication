<?php

namespace App\Filament\Resources\HostelAllocationResource\Pages;

use App\Filament\Resources\HostelAllocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\HostelAllocation;

class ListHostelAllocations extends ListRecords
{
    protected static string $resource = HostelAllocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন বরাদ্দ'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'সকল' => Tab::make()
                ->badge(fn() => HostelAllocation::count()),

            'সক্রিয়' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active'))
                ->badge(fn() => HostelAllocation::where('status', 'active')->count())
                ->badgeColor('success'),

            'ছেড়ে দিয়েছে' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'vacated'))
                ->badge(fn() => HostelAllocation::where('status', 'vacated')->count())
                ->badgeColor('gray'),
        ];
    }
}
