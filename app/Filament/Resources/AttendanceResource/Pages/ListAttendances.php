<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('bulkEntry')
                ->label('বাল্ক এন্ট্রি')
                ->icon('heroicon-o-clipboard-document-list')
                ->url(AttendanceResource::getUrl('bulk-entry'))
                ->color('primary'),
            Actions\CreateAction::make()
                ->label('একক এন্ট্রি')
                ->icon('heroicon-o-plus')
                ->color('gray'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'আজ' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('date', today()))
                ->badge(fn() => $this->getModel()::whereDate('date', today())->count()),
            'গতকাল' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('date', today()->subDay()))
                ->badge(fn() => $this->getModel()::whereDate('date', today()->subDay())->count()),
            'এই সপ্তাহ' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]))
                ->badge(fn() => $this->getModel()::whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->count()),
            'এই মাস' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereMonth('date', now()->month)->whereYear('date', now()->year))
                ->badge(fn() => $this->getModel()::whereMonth('date', now()->month)->whereYear('date', now()->year)->count()),
            'সকল' => Tab::make()
                ->badge(fn() => $this->getModel()::count()),
        ];
    }
}
