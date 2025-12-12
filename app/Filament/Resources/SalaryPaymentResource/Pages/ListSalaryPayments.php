<?php

namespace App\Filament\Resources\SalaryPaymentResource\Pages;

use App\Filament\Resources\SalaryPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\SalaryPayment;

class ListSalaryPayments extends ListRecords
{
    protected static string $resource = SalaryPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন বেতন'),
        ];
    }

    public function getTabs(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        return [
            'সকল' => Tab::make()
                ->badge(fn() => SalaryPayment::count()),

            'এই মাস' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('month', $currentMonth)->where('year', $currentYear))
                ->badge(fn() => SalaryPayment::where('month', $currentMonth)->where('year', $currentYear)->count()),

            'বকেয়া' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(fn() => SalaryPayment::where('status', 'pending')->count())
                ->badgeColor('warning'),

            'পরিশোধিত' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'paid'))
                ->badge(fn() => SalaryPayment::where('status', 'paid')->count())
                ->badgeColor('success'),
        ];
    }
}
