<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    protected static ?string $title = 'উপস্থিতি রেকর্ড';

    protected static ?string $modelLabel = 'উপস্থিতি';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'leave' => 'info',
                        'holiday' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'present' => 'উপস্থিত',
                        'absent' => 'অনুপস্থিত',
                        'late' => 'বিলম্বে',
                        'leave' => 'ছুটি',
                        'holiday' => 'ছুটির দিন',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('remarks')
                    ->label('মন্তব্য')
                    ->limit(30)
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options([
                        'present' => 'উপস্থিত',
                        'absent' => 'অনুপস্থিত',
                        'late' => 'বিলম্বে',
                        'leave' => 'ছুটি',
                    ]),
            ])
            ->headerActions([
                // Attendance usually entered via bulk entry page, not individual
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('date', 'desc')
            ->emptyStateHeading('উপস্থিতির রেকর্ড নেই')
            ->emptyStateDescription('এই ছাত্রের জন্য এখনও উপস্থিতি রেকর্ড করা হয়নি');
    }
}
