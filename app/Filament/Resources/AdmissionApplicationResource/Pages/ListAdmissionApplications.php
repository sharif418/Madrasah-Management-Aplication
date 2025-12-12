<?php

namespace App\Filament\Resources\AdmissionApplicationResource\Pages;

use App\Filament\Resources\AdmissionApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\AdmissionApplication;

class ListAdmissionApplications extends ListRecords
{
    protected static string $resource = AdmissionApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন আবেদন'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'সকল' => Tab::make()
                ->badge(fn() => AdmissionApplication::count()),

            'অপেক্ষমাণ' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(fn() => AdmissionApplication::where('status', 'pending')->count())
                ->badgeColor('warning'),

            'অনুমোদিত' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'approved'))
                ->badge(fn() => AdmissionApplication::where('status', 'approved')->count())
                ->badgeColor('success'),

            'প্রত্যাখ্যাত' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'rejected'))
                ->badge(fn() => AdmissionApplication::where('status', 'rejected')->count())
                ->badgeColor('danger'),
        ];
    }
}
