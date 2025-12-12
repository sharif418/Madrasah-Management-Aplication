<?php

namespace App\Filament\Resources\FeeCollectionResource\Pages;

use App\Filament\Resources\FeeCollectionResource;
use App\Models\StudentFee;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFeeCollections extends ListRecords
{
    protected static string $resource = FeeCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('assign')
                ->label('ফি এসাইন করুন')
                ->icon('heroicon-o-plus-circle')
                ->url(FeeCollectionResource::getUrl('assign'))
                ->color('primary'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'সকল' => Tab::make()
                ->badge(fn() => StudentFee::count()),

            'বাকি' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(fn() => StudentFee::where('status', 'pending')->count())
                ->badgeColor('gray'),

            'আংশিক' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'partial'))
                ->badge(fn() => StudentFee::where('status', 'partial')->count())
                ->badgeColor('warning'),

            'পরিশোধিত' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'paid'))
                ->badge(fn() => StudentFee::where('status', 'paid')->count())
                ->badgeColor('success'),

            'মওকুফ' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'waived'))
                ->badge(fn() => StudentFee::where('status', 'waived')->count())
                ->badgeColor('info'),
        ];
    }
}
