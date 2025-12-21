<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryAlbumResource\Pages;
use App\Models\GalleryAlbum;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class GalleryAlbumResource extends BaseResource
{
    protected static ?string $model = GalleryAlbum::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationGroup = 'ওয়েবসাইট';

    protected static ?string $modelLabel = 'গ্যালারি';

    protected static ?string $pluralModelLabel = 'ফটো গ্যালারি';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('অ্যালবাম')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('শিরোনাম')
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('বিবরণ')
                            ->rows(2),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('cover_image')
                                    ->label('কভার ছবি')
                                    ->image()
                                    ->directory('gallery'),

                                Forms\Components\DatePicker::make('event_date')
                                    ->label('তারিখ')
                                    ->native(false),
                            ]),

                        Forms\Components\Toggle::make('is_published')
                            ->label('প্রকাশিত')
                            ->default(true),
                    ]),

                Forms\Components\Section::make('ছবিসমূহ')
                    ->schema([
                        Forms\Components\Repeater::make('photos')
                            ->relationship()
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('ছবি')
                                    ->image()
                                    ->directory('gallery')
                                    ->required(),

                                Forms\Components\TextInput::make('caption')
                                    ->label('ক্যাপশন'),

                                Forms\Components\TextInput::make('order')
                                    ->label('ক্রম')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(3)
                            ->addActionLabel('নতুন ছবি')
                            ->collapsible(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('কভার')
                    ->height(40),

                Tables\Columns\TextColumn::make('title')
                    ->label('শিরোনাম')
                    ->searchable(),

                Tables\Columns\TextColumn::make('photos_count')
                    ->label('ছবি')
                    ->counts('photos')
                    ->badge(),

                Tables\Columns\TextColumn::make('event_date')
                    ->label('তারিখ')
                    ->date('d M Y'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('প্রকাশিত')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->emptyStateHeading('কোন অ্যালবাম নেই');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleryAlbums::route('/'),
            'create' => Pages\CreateGalleryAlbum::route('/create'),
            'edit' => Pages\EditGalleryAlbum::route('/{record}/edit'),
        ];
    }
}
