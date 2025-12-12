<?php

namespace App\Filament\Resources\ClassNameResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'শাখাসমূহ';

    protected static ?string $modelLabel = 'শাখা';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('শাখার নাম')
                    ->required()
                    ->maxLength(50),

                Forms\Components\TextInput::make('capacity')
                    ->label('সর্বোচ্চ ছাত্র')
                    ->numeric()
                    ->default(40),

                Forms\Components\Select::make('class_teacher_id')
                    ->label('শ্রেণি শিক্ষক')
                    ->relationship('classTeacher', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\Toggle::make('is_active')
                    ->label('সক্রিয়')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('শাখা')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('classTeacher.name')
                    ->label('শ্রেণি শিক্ষক')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('ধারণক্ষমতা')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('ছাত্র')
                    ->counts('students')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('শাখা যোগ করুন'),
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
            ->emptyStateHeading('শাখা নেই')
            ->emptyStateDescription('এই শ্রেণিতে শাখা যোগ করুন');
    }
}
