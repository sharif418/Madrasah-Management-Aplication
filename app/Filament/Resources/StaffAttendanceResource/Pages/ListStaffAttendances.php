<?php

namespace App\Filament\Resources\StaffAttendanceResource\Pages;

use App\Filament\Resources\StaffAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\StaffAttendance;

class ListStaffAttendances extends ListRecords
{
    protected static string $resource = StaffAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('bulk')
                ->label('বাল্ক হাজিরা')
                ->icon('heroicon-o-users')
                ->color('success')
                ->url(fn() => StaffAttendanceResource::getUrl('bulk')),
            Actions\CreateAction::make()->label('নতুন এন্ট্রি'),
        ];
    }

    public function getTabs(): array
    {
        $today = today();

        return [
            'সকল' => Tab::make()
                ->badge(fn() => StaffAttendance::count()),

            'আজকে' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('date', $today))
                ->badge(fn() => StaffAttendance::whereDate('date', $today)->count()),

            'শিক্ষক' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('attendee_type', 'teacher'))
                ->badge(fn() => StaffAttendance::where('attendee_type', 'teacher')->count())
                ->badgeColor('info'),

            'কর্মচারী' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('attendee_type', 'staff'))
                ->badge(fn() => StaffAttendance::where('attendee_type', 'staff')->count())
                ->badgeColor('warning'),
        ];
    }
}
