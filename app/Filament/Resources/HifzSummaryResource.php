<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HifzSummaryResource\Pages;
use App\Models\HifzSummary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HifzSummaryResource extends Resource
{
    protected static ?string $model = HifzSummary::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'হিফজ ও কিতাব';

    protected static ?string $modelLabel = 'হিফজ সারাংশ';

    protected static ?string $pluralModelLabel = 'হাফেজ তালিকা';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('হিফজ সারাংশ')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('ছাত্র')
                            ->relationship('student', 'name', fn(Builder $query) => $query->where('status', 'active'))
                            ->required()
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name_bn} ({$record->admission_no})"),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('current_para')
                                    ->label('বর্তমান পারা')
                                    ->options(\App\Models\HifzProgress::paraOptions())
                                    ->native(false),

                                Forms\Components\TextInput::make('total_para_completed')
                                    ->label('সম্পন্ন পারা সংখ্যা')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(30),

                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(HifzSummary::statusOptions())
                                    ->default('ongoing')
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('hifz_start_date')
                                    ->label('হিফজ শুরুর তারিখ')
                                    ->native(false),

                                Forms\Components\DatePicker::make('hifz_complete_date')
                                    ->label('সম্পন্ন তারিখ')
                                    ->native(false),
                            ]),

                        Forms\Components\Toggle::make('is_hafiz')
                            ->label('হাফেজ')
                            ->helperText('৩০ পারা সম্পন্ন করলে স্বয়ংক্রিয় হবে'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('ছাত্র')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn($record) => $record->student?->admission_no),

                Tables\Columns\TextColumn::make('current_para')
                    ->label('বর্তমান পারা')
                    ->formatStateUsing(fn($state) => $state ? "পারা {$state}" : '-')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('total_para_completed')
                    ->label('সম্পন্ন')
                    ->suffix('/30')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('অগ্রগতি')
                    ->suffix('%')
                    ->badge()
                    ->color(fn($state) => $state >= 100 ? 'success' : ($state >= 50 ? 'warning' : 'gray')),

                Tables\Columns\IconColumn::make('is_hafiz')
                    ->label('হাফেজ')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->trueColor('success'),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => HifzSummary::statusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'ongoing' => 'info',
                        'completed' => 'success',
                        'paused' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('hifz_start_date')
                    ->label('শুরু')
                    ->date('d M Y')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(HifzSummary::statusOptions()),

                Tables\Filters\TernaryFilter::make('is_hafiz')
                    ->label('হাফেজ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateHeading('কোন হিফজ সারাংশ নেই')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHifzSummaries::route('/'),
            'create' => Pages\CreateHifzSummary::route('/create'),
            'edit' => Pages\EditHifzSummary::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_hafiz', true)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
