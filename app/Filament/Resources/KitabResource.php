<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KitabResource\Pages;
use App\Models\Kitab;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class KitabResource extends BaseResource
{
    protected static ?string $model = Kitab::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'হিফজ ও কিতাব';

    protected static ?string $modelLabel = 'কিতাব';

    protected static ?string $pluralModelLabel = 'কিতাব তালিকা';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('কিতাব তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('কিতাবের নাম')
                                    ->placeholder('মিশকাত শরীফ, হেদায়া, কাফিয়া')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('name_en')
                                    ->label('ইংরেজি নাম'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('author')
                                    ->label('লেখক'),

                                Forms\Components\Select::make('class_id')
                                    ->label('শ্রেণি')
                                    ->relationship('class', 'name')
                                    ->native(false)
                                    ->preload(),

                                Forms\Components\TextInput::make('total_chapters')
                                    ->label('মোট অধ্যায়')
                                    ->numeric(),
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
                    ->label('কিতাবের নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('author')
                    ->label('লেখক')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('class.name')
                    ->label('শ্রেণি')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('total_chapters')
                    ->label('অধ্যায়')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('progress_count')
                    ->label('পড়ানো হয়েছে')
                    ->counts('progress')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateHeading('কোন কিতাব নেই')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKitabs::route('/'),
            'create' => Pages\CreateKitab::route('/create'),
            'edit' => Pages\EditKitab::route('/{record}/edit'),
        ];
    }
}
