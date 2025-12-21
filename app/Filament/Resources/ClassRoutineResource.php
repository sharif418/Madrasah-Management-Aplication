<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassRoutineResource\Pages;
use App\Models\ClassRoutine;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClassRoutineResource extends BaseResource
{
    protected static ?string $model = ClassRoutine::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'একাডেমিক সেটআপ';

    protected static ?string $modelLabel = 'ক্লাস রুটিন';

    protected static ?string $pluralModelLabel = 'ক্লাস রুটিন';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('রুটিন তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('academic_year_id')
                                    ->label('শিক্ষাবর্ষ')
                                    ->relationship('academicYear', 'name')
                                    ->required()
                                    ->native(false)
                                    ->default(fn() => \App\Models\AcademicYear::where('is_current', true)->first()?->id),

                                Forms\Components\Select::make('class_id')
                                    ->label('শ্রেণি')
                                    ->relationship('class', 'name')
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('section_id')
                                    ->label('শাখা')
                                    ->relationship('section', 'name')
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('day')
                                    ->label('দিন')
                                    ->options(ClassRoutine::dayOptions())
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('subject_id')
                                    ->label('বিষয়')
                                    ->relationship('subject', 'name')
                                    ->required()
                                    ->native(false)
                                    ->preload(),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TimePicker::make('start_time')
                                    ->label('শুরু')
                                    ->required()
                                    ->native(false),

                                Forms\Components\TimePicker::make('end_time')
                                    ->label('শেষ')
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('room')
                                    ->label('রুম'),
                            ]),

                        Forms\Components\Select::make('teacher_id')
                            ->label('শিক্ষক')
                            ->relationship('teacher', 'name')
                            ->native(false)
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('class.name')
                    ->label('শ্রেণি')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('section.name')
                    ->label('শাখা')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('day')
                    ->label('দিন')
                    ->formatStateUsing(fn($state) => ClassRoutine::dayOptions()[$state] ?? $state)
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('বিষয়')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('শুরু')
                    ->time('h:i A'),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('শেষ')
                    ->time('h:i A'),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('শিক্ষক'),

                Tables\Columns\TextColumn::make('room')
                    ->label('রুম')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_id')
                    ->label('শ্রেণি')
                    ->relationship('class', 'name'),

                Tables\Filters\SelectFilter::make('day')
                    ->label('দিন')
                    ->options(ClassRoutine::dayOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->emptyStateHeading('কোন রুটিন নেই')
            ->emptyStateIcon('heroicon-o-clock');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassRoutines::route('/'),
            'create' => Pages\CreateClassRoutine::route('/create'),
            'edit' => Pages\EditClassRoutine::route('/{record}/edit'),
        ];
    }
}
