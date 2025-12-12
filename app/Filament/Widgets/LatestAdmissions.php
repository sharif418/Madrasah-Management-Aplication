<?php

namespace App\Filament\Widgets;

use App\Models\AdmissionApplication;
use App\Models\StudentFee;
use App\Models\Event;
use Filament\Widgets\Widget;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestAdmissions extends BaseWidget
{
    protected static ?string $heading = 'সাম্প্রতিক ভর্তি আবেদন';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AdmissionApplication::query()
                    ->where('status', 'pending')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('application_no')
                    ->label('আবেদন নং')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('student_name')
                    ->label('ছাত্রের নাম')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('class.name')
                    ->label('শ্রেণি'),

                Tables\Columns\TextColumn::make('father_phone')
                    ->label('মোবাইল'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('আবেদনের তারিখ')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => \App\Models\AdmissionApplication::statusOptions()[$state] ?? $state)
                    ->color('warning'),
            ])
            ->paginated(false)
            ->emptyStateHeading('কোন নতুন আবেদন নেই');
    }
}
