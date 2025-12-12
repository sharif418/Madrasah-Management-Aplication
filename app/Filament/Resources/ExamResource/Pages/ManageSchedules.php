<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use App\Models\Subject;
use Filament\Actions;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class ManageSchedules extends ManageRelatedRecords
{
    protected static string $resource = ExamResource::class;

    protected static string $relationship = 'schedules';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function getNavigationLabel(): string
    {
        return 'পরীক্ষার সূচি';
    }

    public function getTitle(): string
    {
        return $this->getRecord()->name . ' - পরীক্ষার সূচি';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('subject_id')
                            ->label('বিষয়')
                            ->options(function () {
                                $classId = $this->getRecord()->class_id;
                                return Subject::whereHas('classes', function ($query) use ($classId) {
                                    $query->where('classes.id', $classId);
                                })->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->native(false),

                        Forms\Components\DatePicker::make('exam_date')
                            ->label('পরীক্ষার তারিখ')
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

                        Forms\Components\TextInput::make('pass_marks')
                            ->label('পাস নম্বর')
                            ->numeric()
                            ->default(33)
                            ->required(),
                    ]),

                Forms\Components\TextInput::make('room')
                    ->label('কক্ষ/হলের নাম')
                    ->maxLength(100),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subject.name')
            ->columns([
                Tables\Columns\TextColumn::make('exam_date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('বিষয়')
                    ->weight('bold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('শুরু')
                    ->time('h:i A'),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('শেষ')
                    ->time('h:i A'),

                Tables\Columns\TextColumn::make('full_marks')
                    ->label('পূর্ণ নম্বর')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('pass_marks')
                    ->label('পাস নম্বর')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('room')
                    ->label('কক্ষ')
                    ->placeholder('-'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('বিষয় যোগ করুন'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('exam_date')
            ->emptyStateHeading('সূচি নেই')
            ->emptyStateDescription('পরীক্ষার সূচি যোগ করুন');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('পরীক্ষার তালিকায়')
                ->icon('heroicon-o-arrow-left')
                ->url(ExamResource::getUrl('index')),
            Actions\Action::make('marks')
                ->label('নম্বর এন্ট্রি')
                ->icon('heroicon-o-pencil-square')
                ->url(fn() => ExamResource::getUrl('marks-entry', ['record' => $this->getRecord()]))
                ->color('success'),
        ];
    }
}
