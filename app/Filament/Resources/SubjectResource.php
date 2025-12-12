<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Models\ClassName;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'একাডেমিক সেটআপ';

    protected static ?string $modelLabel = 'বিষয়';

    protected static ?string $pluralModelLabel = 'বিষয়সমূহ';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('বিষয়ের তথ্য')
                    ->description('বিষয় সম্পর্কিত বিস্তারিত তথ্য')
                    ->icon('heroicon-o-book-open')
                    ->schema([
                        Forms\Components\CheckboxList::make('classes')
                            ->label('শ্রেণি সমূহ')
                            ->relationship('classes', 'name')
                            ->columns(4)
                            ->required()
                            ->helperText('এই বিষয়টি কোন কোন শ্রেণিতে পড়ানো হবে')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('বিষয়ের নাম (বাংলায়)')
                                    ->placeholder('যেমন: কুরআন মাজিদ, হাদিস শরীফ')
                                    ->required()
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true),

                                Forms\Components\TextInput::make('name_en')
                                    ->label('বিষয়ের নাম (ইংরেজিতে)')
                                    ->placeholder('e.g., Quran, Hadith')
                                    ->maxLength(100),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('code')
                                    ->label('বিষয় কোড')
                                    ->placeholder('যেমন: QUR, HAD, FIQ')
                                    ->required()
                                    ->maxLength(10)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('ইংরেজি বড় হাতের অক্ষরে'),

                                Forms\Components\Select::make('type')
                                    ->label('বিষয়ের ধরণ')
                                    ->options([
                                        'religious' => 'দ্বীনী/ধর্মীয়',
                                        'theory' => 'তত্ত্বীয়',
                                        'practical' => 'ব্যবহারিক',
                                        'language' => 'ভাষা',
                                        'general' => 'সাধারণ',
                                    ])
                                    ->default('religious')
                                    ->required(),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('সক্রিয়')
                                    ->default(true),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('full_marks')
                                    ->label('ডিফল্ট পূর্ণ নম্বর')
                                    ->numeric()
                                    ->default(100)
                                    ->helperText('শ্রেণি অনুযায়ী পরিবর্তন করা যাবে'),

                                Forms\Components\TextInput::make('pass_marks')
                                    ->label('ডিফল্ট পাস নম্বর')
                                    ->numeric()
                                    ->default(33),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('বিবরণ')
                            ->placeholder('বিষয় সম্পর্কে সংক্ষিপ্ত বিবরণ...')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('classes.name')
                    ->label('শ্রেণি সমূহ')
                    ->badge()
                    ->color('success')
                    ->wrap()
                    ->separator(', '),

                Tables\Columns\TextColumn::make('code')
                    ->label('কোড')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('name')
                    ->label('বিষয়ের নাম')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn(Subject $record): ?string => $record->name_en),

                Tables\Columns\TextColumn::make('type')
                    ->label('ধরণ')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'religious' => 'success',
                        'theory' => 'info',
                        'practical' => 'warning',
                        'language' => 'primary',
                        'general' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'religious' => 'দ্বীনী',
                        'theory' => 'তত্ত্বীয়',
                        'practical' => 'ব্যবহারিক',
                        'language' => 'ভাষা',
                        'general' => 'সাধারণ',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('full_marks')
                    ->label('পূর্ণ নম্বর')
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('pass_marks')
                    ->label('পাস নম্বর')
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('classes_count')
                    ->label('শ্রেণি সংখ্যা')
                    ->counts('classes')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('classes')
                    ->label('শ্রেণি')
                    ->relationship('classes', 'name'),

                Tables\Filters\SelectFilter::make('type')
                    ->label('ধরণ')
                    ->options([
                        'religious' => 'দ্বীনী/ধর্মীয়',
                        'theory' => 'তত্ত্বীয়',
                        'practical' => 'ব্যবহারিক',
                        'language' => 'ভাষা',
                        'general' => 'সাধারণ',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('স্ট্যাটাস'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name')
            ->emptyStateHeading('কোন বিষয় নেই')
            ->emptyStateDescription('নতুন বিষয় যোগ করতে নিচের বাটনে ক্লিক করুন')
            ->emptyStateIcon('heroicon-o-book-open');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
