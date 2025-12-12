<?php

namespace App\Filament\Resources\LeaveApplicationResource\Pages;

use App\Filament\Resources\LeaveApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLeaveApplications extends ListRecords
{
    protected static string $resource = LeaveApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('নতুন আবেদন')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'অপেক্ষমাণ' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(fn() => $this->getModel()::where('status', 'pending')->count())
                ->badgeColor('warning'),
            'অনুমোদিত' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'approved'))
                ->badge(fn() => $this->getModel()::where('status', 'approved')->count())
                ->badgeColor('success'),
            'প্রত্যাখ্যাত' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'rejected'))
                ->badge(fn() => $this->getModel()::where('status', 'rejected')->count())
                ->badgeColor('danger'),
            'সকল' => Tab::make()
                ->badge(fn() => $this->getModel()::count()),
        ];
    }
}
