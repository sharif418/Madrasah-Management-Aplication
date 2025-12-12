<?php

namespace App\Filament\Student\Pages;

use App\Models\Notice;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class Notices extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static string $view = 'filament.student.pages.notices';

    protected static ?string $title = 'নোটিশ';

    protected static ?string $navigationLabel = 'নোটিশ';

    protected static ?int $navigationSort = 6;

    public function table(Table $table): Table
    {
        $student = Auth::user()->student;

        return $table
            ->query(
                Notice::query()
                    ->where('is_published', true)
                    ->where(function ($query) use ($student) {
                        $query->where('audience', 'all')
                            ->orWhere('audience', 'students');

                        if ($student?->class_id) {
                            $query->orWhere(function ($q) use ($student) {
                                $q->where('audience', 'specific_class')
                                    ->where('class_id', $student->class_id);
                            });
                        }
                    })
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    })
            )
            ->columns([
                TextColumn::make('title')
                    ->label('শিরোনাম')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('type')
                    ->label('ধরণ')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'general' => 'সাধারণ',
                        'academic' => 'একাডেমিক',
                        'exam' => 'পরীক্ষা',
                        'fee' => 'ফি',
                        'event' => 'ইভেন্ট',
                        'urgent' => 'জরুরি',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'exam' => 'warning',
                        'fee' => 'info',
                        'event' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('publish_date')
                    ->label('তারিখ')
                    ->date('d M, Y')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('মেয়াদ')
                    ->date('d M, Y')
                    ->placeholder('চলমান'),
            ])
            ->defaultSort('publish_date', 'desc')
            ->striped()
            ->emptyStateHeading('কোন নোটিশ নেই')
            ->emptyStateIcon('heroicon-o-bell');
    }
}
