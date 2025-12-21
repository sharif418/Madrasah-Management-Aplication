<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends BaseResource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'ওয়েবসাইট';

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'সাধারণ প্রশ্ন';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('প্রশ্ন-উত্তর')
                    ->schema([
                        Forms\Components\TextInput::make('question')
                            ->label('প্রশ্ন')
                            ->required(),

                        Forms\Components\RichEditor::make('answer')
                            ->label('উত্তর')
                            ->required(),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('category')
                                    ->label('ক্যাটাগরি')
                                    ->options(Faq::categoryOptions())
                                    ->native(false),

                                Forms\Components\TextInput::make('order')
                                    ->label('ক্রম')
                                    ->numeric()
                                    ->default(0),

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
                Tables\Columns\TextColumn::make('question')
                    ->label('প্রশ্ন')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('category')
                    ->label('ক্যাটাগরি')
                    ->badge()
                    ->formatStateUsing(fn($state) => Faq::categoryOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('order')
                    ->label('ক্রম')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('প্রকাশিত')
                    ->boolean(),
            ])
            ->reorderable('order')
            ->defaultSort('order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->emptyStateHeading('কোন FAQ নেই');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
