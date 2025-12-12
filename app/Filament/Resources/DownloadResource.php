<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DownloadResource\Pages;
use App\Models\Download;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DownloadResource extends Resource
{
    protected static ?string $model = Download::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationGroup = 'ওয়েবসাইট';

    protected static ?string $modelLabel = 'ডাউনলোড';

    protected static ?string $pluralModelLabel = 'ডাউনলোড';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ডাউনলোড')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('শিরোনাম')
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('বিবরণ')
                            ->rows(2),

                        Forms\Components\FileUpload::make('file_path')
                            ->label('ফাইল')
                            ->directory('downloads')
                            ->required()
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.ms-excel', 'image/*']),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('category')
                                    ->label('ক্যাটাগরি')
                                    ->options(Download::categoryOptions())
                                    ->native(false),

                                Forms\Components\Select::make('file_type')
                                    ->label('ফাইল টাইপ')
                                    ->options(Download::fileTypeOptions())
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
                Tables\Columns\TextColumn::make('title')
                    ->label('শিরোনাম')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('ক্যাটাগরি')
                    ->badge()
                    ->formatStateUsing(fn($state) => Download::categoryOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('file_type')
                    ->label('টাইপ')
                    ->formatStateUsing(fn($state) => Download::fileTypeOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('download_count')
                    ->label('ডাউনলোড')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('প্রকাশিত')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->emptyStateHeading('কোন ডাউনলোড নেই');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDownloads::route('/'),
            'create' => Pages\CreateDownload::route('/create'),
            'edit' => Pages\EditDownload::route('/{record}/edit'),
        ];
    }
}
