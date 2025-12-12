<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'লাইব্রেরি';

    protected static ?string $modelLabel = 'বই';

    protected static ?string $pluralModelLabel = 'বইসমূহ';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('বইয়ের তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('বইয়ের নাম (বাংলা)')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('title_en')
                                    ->label('বইয়ের নাম (ইংরেজি)')
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label('ক্যাটাগরি')
                                    ->relationship('category', 'name', fn(Builder $query) => $query->active())
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\TextInput::make('author')
                                    ->label('লেখক'),

                                Forms\Components\TextInput::make('publisher')
                                    ->label('প্রকাশক'),
                            ]),

                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('isbn')
                                    ->label('ISBN'),

                                Forms\Components\TextInput::make('publish_year')
                                    ->label('প্রকাশ সাল')
                                    ->numeric()
                                    ->maxLength(4),

                                Forms\Components\TextInput::make('edition')
                                    ->label('সংস্করণ'),

                                Forms\Components\Select::make('language')
                                    ->label('ভাষা')
                                    ->options(Book::languageOptions())
                                    ->default('বাংলা')
                                    ->native(false),
                            ]),
                    ]),

                Forms\Components\Section::make('স্টক ও মূল্য')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('total_copies')
                                    ->label('মোট কপি')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required(),

                                Forms\Components\TextInput::make('available_copies')
                                    ->label('বর্তমান কপি')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0),

                                Forms\Components\TextInput::make('shelf_location')
                                    ->label('তাকের অবস্থান')
                                    ->placeholder('যেমন: A-3-5'),

                                Forms\Components\TextInput::make('price')
                                    ->label('মূল্য')
                                    ->numeric()
                                    ->prefix('৳'),
                            ]),
                    ]),

                Forms\Components\Section::make('অতিরিক্ত তথ্য')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('বইয়ের কভার')
                            ->image()
                            ->directory('books'),

                        Forms\Components\Textarea::make('description')
                            ->label('বিবরণ')
                            ->rows(3),

                        Forms\Components\Toggle::make('is_available')
                            ->label('উপলব্ধ')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn() => 'https://via.placeholder.com/40?text=📚'),

                Tables\Columns\TextColumn::make('title')
                    ->label('বইয়ের নাম')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn($record) => $record->author),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('ক্যাটাগরি')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('language')
                    ->label('ভাষা')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('available_copies')
                    ->label('উপলব্ধ')
                    ->alignCenter()
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('total_copies')
                    ->label('মোট')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('shelf_location')
                    ->label('অবস্থান')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_available')
                    ->label('উপলব্ধ')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('ক্যাটাগরি')
                    ->relationship('category', 'name')
                    ->preload(),

                Tables\Filters\SelectFilter::make('language')
                    ->label('ভাষা')
                    ->options(Book::languageOptions()),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('উপলব্ধ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('title')
            ->emptyStateHeading('কোন বই নেই')
            ->emptyStateDescription('নতুন বই যোগ করুন')
            ->emptyStateIcon('heroicon-o-book-open');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }
}
