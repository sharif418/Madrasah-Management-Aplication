<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectTeacherResource\Pages;
use App\Models\SubjectTeacher;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class SubjectTeacherResource extends BaseResource
{
    protected static ?string $model = SubjectTeacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationGroup = 'একাডেমিক সেটআপ';

    protected static ?string $modelLabel = 'বিষয় শিক্ষক';

    protected static ?string $pluralModelLabel = 'বিষয় শিক্ষক অ্যাসাইনমেন্ট';

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('বিষয় শিক্ষক অ্যাসাইনমেন্ট')
                    ->description('কোন শ্রেণিতে কোন বিষয় কোন শিক্ষক পড়াবেন')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('academic_year_id')
                                    ->label('শিক্ষাবর্ষ')
                                    ->relationship('academicYear', 'name')
                                    ->required()
                                    ->native(false)
                                    ->default(fn() => AcademicYear::where('is_current', true)->first()?->id),

                                Forms\Components\Select::make('class_id')
                                    ->label('শ্রেণি')
                                    ->relationship('class', 'name')
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('section_id')
                                    ->label('শাখা')
                                    ->relationship('section', 'name')
                                    ->native(false),

                                Forms\Components\Select::make('subject_id')
                                    ->label('বিষয়')
                                    ->relationship('subject', 'name')
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\Select::make('teacher_id')
                                    ->label('শিক্ষক')
                                    ->relationship('teacher', 'name')
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->searchable(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('শিক্ষাবর্ষ')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('class.name')
                    ->label('শ্রেণি')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('section.name')
                    ->label('শাখা')
                    ->placeholder('সকল'),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('বিষয়')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('শিক্ষক')
                    ->searchable()
                    ->icon('heroicon-o-user'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('শিক্ষাবর্ষ')
                    ->relationship('academicYear', 'name'),

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('শ্রেণি')
                    ->relationship('class', 'name'),

                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label('শিক্ষক')
                    ->relationship('teacher', 'name'),
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
            ->emptyStateHeading('কোন অ্যাসাইনমেন্ট নেই')
            ->emptyStateDescription('বিষয়ে শিক্ষক অ্যাসাইন করতে উপরের বাটনে ক্লিক করুন')
            ->emptyStateIcon('heroicon-o-user-plus');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjectTeachers::route('/'),
            'create' => Pages\CreateSubjectTeacher::route('/create'),
            'edit' => Pages\EditSubjectTeacher::route('/{record}/edit'),
        ];
    }
}
