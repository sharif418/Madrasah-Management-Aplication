<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeResource\Pages;
use App\Models\Grade;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'পরীক্ষা ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'গ্রেড';

    protected static ?string $pluralModelLabel = 'গ্রেড সেটআপ';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('গ্রেড সেটআপ')
                    ->description('গ্রেডিং সিস্টেম কনফিগার করুন')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('গ্রেড')
                                    ->placeholder('যেমন: A+, A, A-, B+')
                                    ->required()
                                    ->maxLength(10),

                                Forms\Components\TextInput::make('grade_point')
                                    ->label('গ্রেড পয়েন্ট')
                                    ->numeric()
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->maxValue(5)
                                    ->required(),

                                Forms\Components\TextInput::make('remarks')
                                    ->label('মন্তব্য')
                                    ->placeholder('যেমন: Excellent, Very Good')
                                    ->maxLength(50),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('min_marks')
                                    ->label('সর্বনিম্ন নম্বর (%)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->required()
                                    ->suffix('%'),

                                Forms\Components\TextInput::make('max_marks')
                                    ->label('সর্বোচ্চ নম্বর (%)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->required()
                                    ->suffix('%')
                                    ->gte('min_marks'),
                            ]),

                        Forms\Components\Toggle::make('is_active')
                            ->label('সক্রিয়')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('গ্রেড')
                    ->badge()
                    ->color('primary')
                    ->weight('bold')
                    ->size('lg'),

                Tables\Columns\TextColumn::make('grade_point')
                    ->label('পয়েন্ট')
                    ->alignCenter()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('min_marks')
                    ->label('সর্বনিম্ন')
                    ->suffix('%')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('max_marks')
                    ->label('সর্বোচ্চ')
                    ->suffix('%')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('remarks')
                    ->label('মন্তব্য')
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('স্ট্যাটাস'),
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
            ->defaultSort('grade_point', 'desc')
            ->striped()
            ->emptyStateHeading('গ্রেড সেটআপ নেই')
            ->emptyStateDescription('গ্রেডিং সিস্টেম কনফিগার করুন')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'edit' => Pages\EditGrade::route('/{record}/edit'),
        ];
    }
}
