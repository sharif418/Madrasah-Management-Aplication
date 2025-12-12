<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Setting;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন সেটিং'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'সকল' => Tab::make(),
            'সাধারণ' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('group', 'general')),
            'SMS' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('group', 'sms')),
            'পেমেন্ট' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('group', 'payment')),
        ];
    }
}
