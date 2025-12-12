<?php

namespace App\Filament\Resources\SmsLogResource\Pages;

use App\Filament\Resources\SmsLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\SmsLog;

class ListSmsLogs extends ListRecords
{
    protected static string $resource = SmsLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন SMS'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'সকল' => Tab::make()
                ->badge(fn() => SmsLog::count()),

            'প্রেরিত' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'sent'))
                ->badge(fn() => SmsLog::where('status', 'sent')->count())
                ->badgeColor('success'),

            'ব্যর্থ' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'failed'))
                ->badge(fn() => SmsLog::where('status', 'failed')->count())
                ->badgeColor('danger'),

            'অপেক্ষমাণ' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(fn() => SmsLog::where('status', 'pending')->count())
                ->badgeColor('warning'),
        ];
    }
}
