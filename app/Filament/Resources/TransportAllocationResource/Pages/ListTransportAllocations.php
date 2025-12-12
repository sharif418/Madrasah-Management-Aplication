<?php

namespace App\Filament\Resources\TransportAllocationResource\Pages;

use App\Filament\Resources\TransportAllocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\TransportAllocation;

class ListTransportAllocations extends ListRecords
{
    protected static string $resource = TransportAllocationResource::class;

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
                ->badge(fn() => TransportAllocation::count()),

            'সক্রিয়' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active'))
                ->badge(fn() => TransportAllocation::where('status', 'active')->count())
                ->badgeColor('success'),

            'নিষ্ক্রিয়' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'inactive'))
                ->badge(fn() => TransportAllocation::where('status', 'inactive')->count())
                ->badgeColor('gray'),
        ];
    }
}
