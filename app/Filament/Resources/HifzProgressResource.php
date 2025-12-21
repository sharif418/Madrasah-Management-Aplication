<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HifzProgressResource\Pages;
use App\Models\HifzProgress;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HifzProgressResource extends BaseResource
{
    protected static ?string $model = HifzProgress::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'হিফজ ও কিতাব';

    protected static ?string $modelLabel = 'হিফজ প্রগ্রেস';

    protected static ?string $pluralModelLabel = 'দৈনিক হিফজ';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ছাত্র ও তারিখ')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('student_id')
                                    ->label('ছাত্র')
                                    ->relationship('student', 'name', fn(Builder $query) => $query->where('status', 'active'))
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->searchable()
                                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name_bn} ({$record->admission_no})"),

                                Forms\Components\Select::make('academic_year_id')
                                    ->label('শিক্ষাবর্ষ')
                                    ->relationship('academicYear', 'name')
                                    ->native(false)
                                    ->preload()
                                    ->default(fn() => \App\Models\AcademicYear::where('is_current', true)->first()?->id),

                                Forms\Components\DatePicker::make('date')
                                    ->label('তারিখ')
                                    ->default(now())
                                    ->required()
                                    ->native(false),
                            ]),
                    ]),

                Forms\Components\Section::make('সবক (নতুন পড়া)')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Select::make('sabaq_para')
                                    ->label('পারা')
                                    ->options(HifzProgress::paraOptions())
                                    ->native(false),

                                Forms\Components\TextInput::make('sabaq_surah')
                                    ->label('সূরা'),

                                Forms\Components\TextInput::make('sabaq_ayat_from')
                                    ->label('আয়াত থেকে')
                                    ->numeric(),

                                Forms\Components\TextInput::make('sabaq_ayat_to')
                                    ->label('আয়াত পর্যন্ত')
                                    ->numeric(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('sabaq_lines')
                                    ->label('লাইন সংখ্যা')
                                    ->numeric(),

                                Forms\Components\Select::make('sabaq_quality')
                                    ->label('মান')
                                    ->options(HifzProgress::qualityOptions())
                                    ->native(false),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('সাবকি (গত দিনের পড়া)')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('sabqi_para')
                                    ->label('পারা')
                                    ->options(HifzProgress::paraOptions())
                                    ->native(false),

                                Forms\Components\TextInput::make('sabqi_surah')
                                    ->label('সূরা'),

                                Forms\Components\Select::make('sabqi_quality')
                                    ->label('মান')
                                    ->options(HifzProgress::qualityOptions())
                                    ->native(false),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('মনযিল (দোহার)')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('manzil_para_from')
                                    ->label('পারা থেকে')
                                    ->options(HifzProgress::paraOptions())
                                    ->native(false),

                                Forms\Components\Select::make('manzil_para_to')
                                    ->label('পারা পর্যন্ত')
                                    ->options(HifzProgress::paraOptions())
                                    ->native(false),

                                Forms\Components\Select::make('manzil_quality')
                                    ->label('মান')
                                    ->options(HifzProgress::qualityOptions())
                                    ->native(false),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('শিক্ষকের মন্তব্য')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('teacher_id')
                                    ->label('শিক্ষক')
                                    ->relationship('teacher', 'name')
                                    ->native(false)
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\Textarea::make('teacher_remarks')
                                    ->label('মন্তব্য')
                                    ->rows(2),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('ছাত্র')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('sabaq_para')
                    ->label('সবক পারা')
                    ->formatStateUsing(fn($state) => $state ? "পারা {$state}" : '-')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('sabaq_quality')
                    ->label('সবক মান')
                    ->badge()
                    ->formatStateUsing(fn($state) => HifzProgress::qualityOptions()[$state] ?? '-')
                    ->color(fn($state) => HifzProgress::getQualityColor($state ?? '')),

                Tables\Columns\TextColumn::make('sabqi_quality')
                    ->label('সাবকি মান')
                    ->badge()
                    ->formatStateUsing(fn($state) => HifzProgress::qualityOptions()[$state] ?? '-')
                    ->color(fn($state) => HifzProgress::getQualityColor($state ?? '')),

                Tables\Columns\TextColumn::make('manzil_quality')
                    ->label('মনযিল মান')
                    ->badge()
                    ->formatStateUsing(fn($state) => HifzProgress::qualityOptions()[$state] ?? '-')
                    ->color(fn($state) => HifzProgress::getQualityColor($state ?? '')),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('শিক্ষক')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('student_id')
                    ->label('ছাত্র')
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('sabaq_quality')
                    ->label('সবক মান')
                    ->options(HifzProgress::qualityOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('date', 'desc')
            ->emptyStateHeading('কোন হিফজ রেকর্ড নেই')
            ->emptyStateIcon('heroicon-o-book-open');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHifzProgress::route('/'),
            'create' => Pages\CreateHifzProgress::route('/create'),
            'edit' => Pages\EditHifzProgress::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('date', today())->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
