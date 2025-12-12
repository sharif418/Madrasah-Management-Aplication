<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationGroup = 'ওয়েবসাইট';

    protected static ?string $modelLabel = 'প্রশংসা';

    protected static ?string $pluralModelLabel = 'প্রশংসা';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('প্রশংসা')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('নাম')
                                    ->required(),

                                Forms\Components\Select::make('designation')
                                    ->label('পরিচয়')
                                    ->options(Testimonial::designationOptions())
                                    ->native(false),
                            ]),

                        Forms\Components\FileUpload::make('photo')
                            ->label('ছবি')
                            ->image()
                            ->avatar()
                            ->directory('testimonials'),

                        Forms\Components\Textarea::make('content')
                            ->label('মন্তব্য')
                            ->required()
                            ->rows(4),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('rating')
                                    ->label('রেটিং')
                                    ->options([
                                        5 => '⭐⭐⭐⭐⭐ (5)',
                                        4 => '⭐⭐⭐⭐ (4)',
                                        3 => '⭐⭐⭐ (3)',
                                        2 => '⭐⭐ (2)',
                                        1 => '⭐ (1)',
                                    ])
                                    ->native(false),

                                Forms\Components\Toggle::make('is_published')
                                    ->label('প্রকাশিত')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('ছবি')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('নাম')
                    ->searchable(),

                Tables\Columns\TextColumn::make('designation')
                    ->label('পরিচয়')
                    ->formatStateUsing(fn($state) => Testimonial::designationOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('rating')
                    ->label('রেটিং')
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state ?? 0)),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('প্রকাশিত')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->emptyStateHeading('কোন প্রশংসা নেই');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
