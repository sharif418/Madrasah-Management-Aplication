<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassNameResource\Pages;
use App\Filament\Resources\ClassNameResource\RelationManagers;
use App\Models\ClassName;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ActionGroup;

class ClassNameResource extends BaseResource
{
    protected static ?string $model = ClassName::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'একাডেমিক সেটআপ';

    protected static ?string $modelLabel = 'শ্রেণি';

    protected static ?string $pluralModelLabel = 'শ্রেণিসমূহ';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('শ্রেণির তথ্য')
                    ->description('শ্রেণি/মারহালা সম্পর্কিত তথ্য')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('শ্রেণির নাম (বাংলায়)')
                                    ->placeholder('যেমন: ইবতেদায়ী, হিফজ, মুতাওয়াসসিতা')
                                    ->required()
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true),

                                Forms\Components\TextInput::make('name_en')
                                    ->label('শ্রেণির নাম (ইংরেজিতে)')
                                    ->placeholder('e.g., Ibtedayi, Hifz')
                                    ->maxLength(100),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('numeric_name')
                                    ->label('সংখ্যাসূচক নাম')
                                    ->placeholder('যেমন: 1, 2, 3')
                                    ->helperText('রিপোর্টে ব্যবহারের জন্য')
                                    ->maxLength(10),

                                Forms\Components\Select::make('department_id')
                                    ->label('বিভাগ')
                                    ->relationship('department', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('বিভাগের নাম')
                                            ->required(),
                                    ])
                                    ->helperText('কোন বিভাগের অধীনে'),

                                Forms\Components\TextInput::make('order')
                                    ->label('ক্রম')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('সিরিয়াল নম্বর'),
                            ]),

                        Forms\Components\Toggle::make('is_active')
                            ->label('সক্রিয়')
                            ->default(true)
                            ->helperText('নিষ্ক্রিয় করলে ভর্তি ও অন্যান্য ফর্মে দেখাবে না'),
                    ]),

                Forms\Components\Section::make('বিষয়সমূহ')
                    ->description('এই শ্রেণিতে পড়ানো হবে এমন বিষয় নির্বাচন করুন')
                    ->icon('heroicon-o-book-open')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Repeater::make('subjects')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('subject_id')
                                    ->label('বিষয়')
                                    ->relationship('subjects', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('full_marks')
                                    ->label('পূর্ণ নম্বর')
                                    ->numeric()
                                    ->default(100),

                                Forms\Components\TextInput::make('pass_marks')
                                    ->label('পাস নম্বর')
                                    ->numeric()
                                    ->default(33),

                                Forms\Components\Toggle::make('is_optional')
                                    ->label('ঐচ্ছিক')
                                    ->default(false),
                            ])
                            ->columns(5)
                            ->defaultItems(0)
                            ->addActionLabel('বিষয় যোগ করুন')
                            ->reorderable()
                            ->hidden(), // Will implement via relation manager
                    ])
                    ->hidden(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->alignCenter()
                    ->width(50),

                Tables\Columns\TextColumn::make('name')
                    ->label('শ্রেণি')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn(ClassName $record): ?string => $record->name_en),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('বিভাগ')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'হিফজ বিভাগ' => 'success',
                        'কিতাব বিভাগ' => 'info',
                        'নাজেরা বিভাগ' => 'warning',
                        default => 'gray',
                    })
                    ->placeholder('নির্ধারণ করা হয়নি'),

                Tables\Columns\TextColumn::make('sections_count')
                    ->label('শাখা')
                    ->counts('sections')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('ছাত্র')
                    ->counts('students')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('subjects_count')
                    ->label('বিষয়')
                    ->counts('subjects')
                    ->alignCenter()
                    ->badge()
                    ->color('warning')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('আপডেট')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department_id')
                    ->label('বিভাগ')
                    ->relationship('department', 'name')
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('স্ট্যাটাস')
                    ->placeholder('সব')
                    ->trueLabel('সক্রিয়')
                    ->falseLabel('নিষ্ক্রিয়'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('manageSections')
                        ->label('শাখা পরিচালনা')
                        ->icon('heroicon-o-squares-plus')
                        ->url(fn(ClassName $record): string => static::getUrl('sections', ['record' => $record])),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->emptyStateHeading('কোন শ্রেণি নেই')
            ->emptyStateDescription('নতুন শ্রেণি যোগ করতে নিচের বাটনে ক্লিক করুন')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('শ্রেণির তথ্য')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('শ্রেণির নাম'),
                                Infolists\Components\TextEntry::make('name_en')
                                    ->label('ইংরেজি নাম'),
                                Infolists\Components\TextEntry::make('department.name')
                                    ->label('বিভাগ')
                                    ->badge(),
                            ]),

                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('sections_count')
                                    ->label('শাখা সংখ্যা')
                                    ->state(fn(ClassName $record): int => $record->sections()->count()),
                                Infolists\Components\TextEntry::make('students_count')
                                    ->label('ছাত্র সংখ্যা')
                                    ->state(fn(ClassName $record): int => $record->students()->where('status', 'active')->count()),
                                Infolists\Components\TextEntry::make('subjects_count')
                                    ->label('বিষয় সংখ্যা')
                                    ->state(fn(ClassName $record): int => $record->subjects()->count()),
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('স্ট্যাটাস')
                                    ->boolean(),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SectionsRelationManager::class,
            RelationManagers\SubjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassNames::route('/'),
            'create' => Pages\CreateClassName::route('/create'),
            'view' => Pages\ViewClassName::route('/{record}'),
            'edit' => Pages\EditClassName::route('/{record}/edit'),
            'sections' => Pages\ManageSections::route('/{record}/sections'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
