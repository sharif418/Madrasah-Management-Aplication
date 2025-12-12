<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('নতুন ছাত্র ভর্তি')
                ->icon('heroicon-o-plus'),
            Actions\Action::make('import')
                ->label('ইমপোর্ট')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading('ছাত্র ইমপোর্ট')
                ->modalDescription('এই ফিচার শীঘ্রই আসছে। Excel/CSV ফাইল থেকে ছাত্র ইমপোর্ট করতে পারবেন।')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('বন্ধ করুন'),
            Actions\Action::make('export')
                ->label('এক্সপোর্ট')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->modalHeading('ছাত্র এক্সপোর্ট')
                ->modalDescription('এই ফিচার শীঘ্রই আসছে। Excel/CSV ফরম্যাটে ছাত্র এক্সপোর্ট করতে পারবেন।')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('বন্ধ করুন'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'সকল' => Tab::make()
                ->badge(fn() => $this->getModel()::count()),
            'সক্রিয়' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active'))
                ->badge(fn() => $this->getModel()::where('status', 'active')->count())
                ->badgeColor('success'),
            'আবাসিক' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_boarder', true)->where('status', 'active'))
                ->badge(fn() => $this->getModel()::where('is_boarder', true)->where('status', 'active')->count())
                ->badgeColor('warning'),
            'বদলি' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'transferred'))
                ->badge(fn() => $this->getModel()::where('status', 'transferred')->count())
                ->badgeColor('gray'),
            'ঝরে পড়া' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'dropped_out'))
                ->badge(fn() => $this->getModel()::where('status', 'dropped_out')->count())
                ->badgeColor('danger'),
        ];
    }
}
