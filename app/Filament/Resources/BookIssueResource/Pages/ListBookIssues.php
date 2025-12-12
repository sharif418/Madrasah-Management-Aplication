<?php

namespace App\Filament\Resources\BookIssueResource\Pages;

use App\Filament\Resources\BookIssueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\BookIssue;

class ListBookIssues extends ListRecords
{
    protected static string $resource = BookIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('নতুন ইস্যু'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'সকল' => Tab::make()
                ->badge(fn() => BookIssue::count()),

            'ইস্যু করা' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'issued'))
                ->badge(fn() => BookIssue::where('status', 'issued')->count())
                ->badgeColor('warning'),

            'মেয়াদোত্তীর্ণ' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'issued')->where('due_date', '<', now()))
                ->badge(fn() => BookIssue::where('status', 'issued')->where('due_date', '<', now())->count())
                ->badgeColor('danger'),

            'ফেরত' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'returned'))
                ->badge(fn() => BookIssue::where('status', 'returned')->count())
                ->badgeColor('success'),
        ];
    }
}
