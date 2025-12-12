<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeStructureResource\Pages;
use App\Models\FeeStructure;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FeeStructureResource extends Resource
{
    protected static ?string $model = FeeStructure::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationGroup = 'ফি ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'ফি কাঠামো';

    protected static ?string $pluralModelLabel = 'ফি কাঠামোসমূহ';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ফি কাঠামো সেটআপ')
                    ->description('শ্রেণিভিত্তিক ফি নির্ধারণ করুন')
                    ->icon('heroicon-o-queue-list')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('fee_type_id')
                                    ->label('ফি এর ধরণ')
                                    ->relationship('feeType', 'name', fn(Builder $query) => $query->where('is_active', true))
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\Select::make('class_id')
                                    ->label('শ্রেণি')
                                    ->relationship('class', 'name', fn(Builder $query) => $query->where('is_active', true)->orderBy('order'))
                                    ->required()
                                    ->native(false)
                                    ->preload(),

                                Forms\Components\Select::make('academic_year_id')
                                    ->label('শিক্ষাবর্ষ')
                                    ->relationship('academicYear', 'name')
                                    ->default(fn() => AcademicYear::current()?->id)
                                    ->required()
                                    ->native(false)
                                    ->preload(),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->label('পরিমাণ (টাকা)')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->required()
                                    ->minValue(0),

                                Forms\Components\TextInput::make('due_day')
                                    ->label('পরিশোধের শেষ দিন')
                                    ->helperText('মাসের কত তারিখের মধ্যে')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(31),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('সক্রিয়')
                                    ->default(true),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('late_fee')
                                    ->label('বিলম্ব জরিমানা (টাকা)')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0),

                                Forms\Components\Textarea::make('description')
                                    ->label('বিবরণ')
                                    ->rows(2),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('feeType.name')
                    ->label('ফি এর ধরণ')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('class.name')
                    ->label('শ্রেণি')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('শিক্ষাবর্ষ')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('পরিমাণ')
                    ->money('BDT')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('late_fee')
                    ->label('বিলম্ব জরিমানা')
                    ->money('BDT')
                    ->alignEnd()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('due_day')
                    ->label('শেষ দিন')
                    ->suffix(' তারিখ')
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('fee_type_id')
                    ->label('ফি এর ধরণ')
                    ->relationship('feeType', 'name')
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('শ্রেণি')
                    ->relationship('class', 'name')
                    ->preload(),

                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('শিক্ষাবর্ষ')
                    ->relationship('academicYear', 'name')
                    ->default(fn() => AcademicYear::current()?->id)
                    ->preload(),

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
            ->defaultSort('class_id')
            ->striped()
            ->emptyStateHeading('কোন ফি কাঠামো নেই')
            ->emptyStateDescription('শ্রেণিভিত্তিক ফি কাঠামো তৈরি করুন')
            ->emptyStateIcon('heroicon-o-queue-list');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeStructures::route('/'),
            'create' => Pages\CreateFeeStructure::route('/create'),
            'edit' => Pages\EditFeeStructure::route('/{record}/edit'),
        ];
    }
}
