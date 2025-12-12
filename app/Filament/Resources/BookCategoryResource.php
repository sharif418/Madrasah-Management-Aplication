<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookCategoryResource\Pages;
use App\Models\BookCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BookCategoryResource extends Resource
{
    protected static ?string $model = BookCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'লাইব্রেরি';

    protected static ?string $modelLabel = 'বই ক্যাটাগরি';

    protected static ?string $pluralModelLabel = 'বই ক্যাটাগরিসমূহ';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('বই ক্যাটাগরি')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('নাম (বাংলা)')
                                    ->placeholder('যেমন: কুরআন, হাদিস, ফিকহ')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('name_en')
                                    ->label('নাম (ইংরেজি)')
                                    ->placeholder('e.g. Quran, Hadith, Fiqh')
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('বিবরণ')
                            ->rows(2),

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
                    ->label('নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name_en')
                    ->label('ইংরেজি নাম')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('books_count')
                    ->label('বই সংখ্যা')
                    ->counts('books')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->emptyStateHeading('কোন ক্যাটাগরি নেই')
            ->emptyStateIcon('heroicon-o-folder');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookCategories::route('/'),
            'create' => Pages\CreateBookCategory::route('/create'),
            'edit' => Pages\EditBookCategory::route('/{record}/edit'),
        ];
    }
}
