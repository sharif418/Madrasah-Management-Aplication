<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamScheduleResource\Pages;
use App\Models\ExamSchedule;
use App\Models\Exam;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExamScheduleResource extends BaseResource
{
    protected static ?string $model = ExamSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'পরীক্ষা ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'পরীক্ষার সময়সূচী';

    protected static ?string $pluralModelLabel = 'পরীক্ষার সময়সূচী';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('পরীক্ষার সময়সূচী')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('exam_id')
                                    ->label('পরীক্ষা')
                                    ->relationship('exam', 'name')
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\Select::make('class_id')
                                    ->label('শ্রেণি')
                                    ->relationship('class', 'name')
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('subject_id')
                                    ->label('বিষয়')
                                    ->relationship('subject', 'name')
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\DatePicker::make('exam_date')
                                    ->label('তারিখ')
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TimePicker::make('start_time')
                                    ->label('শুরু')
                                    ->required()
                                    ->native(false),

                                Forms\Components\TimePicker::make('end_time')
                                    ->label('শেষ')
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('full_marks')
                                    ->label('পূর্ণ নম্বর')
                                    ->numeric()
                                    ->default(100)
                                    ->required(),

                                Forms\Components\TextInput::make('room')
                                    ->label('রুম'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('exam.name')
                    ->label('পরীক্ষা')
                    ->searchable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('class.name')
                    ->label('শ্রেণি')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('বিষয়')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('exam_date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('শুরু')
                    ->time('h:i A'),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('শেষ')
                    ->time('h:i A'),

                Tables\Columns\TextColumn::make('full_marks')
                    ->label('নম্বর')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('room')
                    ->label('রুম')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('exam_id')
                    ->label('পরীক্ষা')
                    ->relationship('exam', 'name'),

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('শ্রেণি')
                    ->relationship('class', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('exam_date', 'asc')
            ->emptyStateHeading('কোন সময়সূচী নেই')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExamSchedules::route('/'),
            'create' => Pages\CreateExamSchedule::route('/create'),
            'edit' => Pages\EditExamSchedule::route('/{record}/edit'),
        ];
    }
}
