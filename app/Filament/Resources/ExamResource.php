<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResource\Pages;
use App\Filament\Resources\ExamResource\RelationManagers;
use App\Models\Exam;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ActionGroup;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'পরীক্ষা ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'পরীক্ষা';

    protected static ?string $pluralModelLabel = 'পরীক্ষাসমূহ';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('পরীক্ষার তথ্য')
                    ->description('পরীক্ষার বিস্তারিত তথ্য পূরণ করুন')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('পরীক্ষার নাম')
                                    ->placeholder('যেমন: প্রথম সাময়িক পরীক্ষা ২০২৫')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Select::make('exam_type_id')
                                    ->label('পরীক্ষার ধরণ')
                                    ->relationship('examType', 'name', fn(Builder $query) => $query->where('is_active', true))
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('নাম')
                                            ->required(),
                                        Forms\Components\TextInput::make('name_en')
                                            ->label('নাম (ইংরেজি)'),
                                    ])
                                    ->createOptionModalHeading('নতুন পরীক্ষার ধরণ'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('academic_year_id')
                                    ->label('শিক্ষাবর্ষ')
                                    ->relationship('academicYear', 'name')
                                    ->default(fn() => AcademicYear::current()?->id)
                                    ->required()
                                    ->native(false)
                                    ->preload(),

                                Forms\Components\Select::make('class_id')
                                    ->label('শ্রেণি')
                                    ->relationship('class', 'name', fn(Builder $query) => $query->where('is_active', true)->orderBy('order'))
                                    ->required()
                                    ->native(false)
                                    ->preload(),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('শুরুর তারিখ')
                                    ->required()
                                    ->native(false),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('শেষ তারিখ')
                                    ->required()
                                    ->native(false)
                                    ->afterOrEqual('start_date'),

                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(Exam::statusOptions())
                                    ->default('upcoming')
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('বিবরণ')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('পরীক্ষার নাম')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('examType.name')
                    ->label('ধরণ')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('class.name')
                    ->label('শ্রেণি')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('শিক্ষাবর্ষ')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('শুরু')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('শেষ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'upcoming' => 'warning',
                        'ongoing' => 'info',
                        'completed' => 'success',
                        'result_published' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => Exam::statusOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('schedules_count')
                    ->label('বিষয়')
                    ->counts('schedules')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('exam_type_id')
                    ->label('ধরণ')
                    ->relationship('examType', 'name')
                    ->preload(),

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('শ্রেণি')
                    ->relationship('class', 'name')
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('শিক্ষাবর্ষ')
                    ->relationship('academicYear', 'name')
                    ->default(fn() => AcademicYear::current()?->id)
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(Exam::statusOptions()),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('দেখুন'),
                    Tables\Actions\EditAction::make()
                        ->label('সম্পাদনা'),
                    Tables\Actions\Action::make('schedule')
                        ->label('সূচি')
                        ->icon('heroicon-o-calendar')
                        ->url(fn(Exam $record): string => ExamResource::getUrl('schedules', ['record' => $record])),
                    Tables\Actions\Action::make('marks')
                        ->label('নম্বর এন্ট্রি')
                        ->icon('heroicon-o-pencil-square')
                        ->url(fn(Exam $record): string => ExamResource::getUrl('marks-entry', ['record' => $record]))
                        ->visible(fn(Exam $record): bool => in_array($record->status, ['ongoing', 'completed'])),
                    Tables\Actions\Action::make('results')
                        ->label('ফলাফল')
                        ->icon('heroicon-o-chart-bar')
                        ->url(fn(Exam $record): string => ExamResource::getUrl('results', ['record' => $record]))
                        ->visible(fn(Exam $record): bool => in_array($record->status, ['completed', 'result_published'])),
                    Tables\Actions\DeleteAction::make()
                        ->label('মুছে ফেলুন'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_date', 'desc')
            ->striped()
            ->emptyStateHeading('কোন পরীক্ষা নেই')
            ->emptyStateDescription('নতুন পরীক্ষা যোগ করতে নিচের বাটনে ক্লিক করুন')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SchedulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'view' => Pages\ViewExam::route('/{record}'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
            'schedules' => Pages\ManageSchedules::route('/{record}/schedules'),
            'marks-entry' => Pages\MarksEntry::route('/{record}/marks-entry'),
            'results' => Pages\ExamResults::route('/{record}/results'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $currentYear = AcademicYear::current();
        if (!$currentYear)
            return null;

        return static::getModel()::where('academic_year_id', $currentYear->id)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
}
