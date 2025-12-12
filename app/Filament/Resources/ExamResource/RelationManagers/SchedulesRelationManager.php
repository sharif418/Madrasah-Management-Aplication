<?php

namespace App\Filament\Resources\ExamResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'পরীক্ষার সূচি';

    protected static ?string $modelLabel = 'সূচি';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subject_id')
                    ->label('বিষয়')
                    ->relationship('subject', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\DatePicker::make('exam_date')
                    ->label('তারিখ')
                    ->required()
                    ->native(false),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TimePicker::make('start_time')
                            ->label('শুরু')
                            ->required(),

                        Forms\Components\TimePicker::make('end_time')
                            ->label('শেষ')
                            ->required(),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('full_marks')
                            ->label('পূর্ণ নম্বর')
                            ->numeric()
                            ->default(100),

                        Forms\Components\TextInput::make('pass_marks')
                            ->label('পাস নম্বর')
                            ->numeric()
                            ->default(33),
                    ]),

                Forms\Components\TextInput::make('room')
                    ->label('কক্ষ'),
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
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('সময়')
                    ->formatStateUsing(
                        fn($record) =>
                        date('h:i A', strtotime($record->start_time)) . ' - ' . date('h:i A', strtotime($record->end_time))
                    ),

                Tables\Columns\TextColumn::make('full_marks')
                    ->label('পূর্ণ নম্বর'),

                Tables\Columns\TextColumn::make('room')
                    ->label('কক্ষ')
                    ->placeholder('-'),
            ])
            ->defaultSort('exam_date')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('সূচি যোগ করুন'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->emptyStateHeading('সূচি নেই');
    }
}
