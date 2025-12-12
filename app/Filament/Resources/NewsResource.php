<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'ওয়েবসাইট';

    protected static ?string $modelLabel = 'সংবাদ';

    protected static ?string $pluralModelLabel = 'সংবাদ';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('সংবাদ')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('শিরোনাম')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, $set) => $set('slug', Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->label('স্লাগ')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('excerpt')
                            ->label('সারসংক্ষেপ')
                            ->rows(2),

                        Forms\Components\RichEditor::make('content')
                            ->label('বিষয়বস্তু')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('featured_image')
                            ->label('ফিচার ইমেজ')
                            ->image()
                            ->directory('news'),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('publish_date')
                                    ->label('প্রকাশের তারিখ')
                                    ->default(now())
                                    ->required()
                                    ->native(false),

                                Forms\Components\Toggle::make('is_published')
                                    ->label('প্রকাশিত')
                                    ->default(true),

                                Forms\Components\Toggle::make('is_featured')
                                    ->label('ফিচার্ড'),
                            ]),

                        Forms\Components\Hidden::make('created_by')
                            ->default(fn() => Auth::id()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('ছবি')
                    ->height(40),

                Tables\Columns\TextColumn::make('title')
                    ->label('শিরোনাম')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('publish_date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('views')
                    ->label('ভিউ')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('ফিচার্ড')
                    ->boolean()
                    ->trueIcon('heroicon-o-star'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('প্রকাশিত')
                    ->boolean(),
            ])
            ->defaultSort('publish_date', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->emptyStateHeading('কোন সংবাদ নেই');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
