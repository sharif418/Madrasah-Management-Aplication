<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Staff;

class ListStaff extends ListRecords
{
    protected static string $resource = StaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন কর্মচারী'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'সকল' => Tab::make()
                ->badge(fn() => Staff::count()),

            'সক্রিয়' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active'))
                ->badge(fn() => Staff::where('status', 'active')->count())
                ->badgeColor('success'),

            'স্থায়ী' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('employment_type', 'permanent'))
                ->badge(fn() => Staff::where('employment_type', 'permanent')->count())
                ->badgeColor('info'),

            'অস্থায়ী' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('employment_type', 'temporary'))
                ->badge(fn() => Staff::where('employment_type', 'temporary')->count())
                ->badgeColor('warning'),
        ];
    }
}
