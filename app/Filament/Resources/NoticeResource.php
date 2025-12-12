<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoticeResource\Pages;
use App\Models\Notice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class NoticeResource extends Resource
{
    protected static ?string $model = Notice::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'যোগাযোগ';

    protected static ?string $modelLabel = 'নোটিশ';

    protected static ?string $pluralModelLabel = 'নোটিশ বোর্ড';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('নোটিশ তথ্য')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('শিরোনাম')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content')
                            ->label('বিষয়বস্তু')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('ধরণ')
                                    ->options(Notice::typeOptions())
                                    ->default('general')
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('audience')
                                    ->label('প্রাপক')
                                    ->options(Notice::audienceOptions())
                                    ->default('all')
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('class_id')
                                    ->label('শ্রেণি (ঐচ্ছিক)')
                                    ->relationship('class', 'name')
                                    ->native(false)
                                    ->preload()
                                    ->placeholder('সকল শ্রেণি'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('publish_date')
                                    ->label('প্রকাশের তারিখ')
                                    ->default(now())
                                    ->required()
                                    ->native(false),

                                Forms\Components\DatePicker::make('expiry_date')
                                    ->label('মেয়াদ শেষ')
                                    ->native(false)
                                    ->placeholder('কোন মেয়াদ নেই'),
                            ]),

                        Forms\Components\FileUpload::make('attachment')
                            ->label('সংযুক্তি')
                            ->directory('notices')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(5120),

                        Forms\Components\Toggle::make('is_published')
                            ->label('প্রকাশিত')
                            ->default(true),

                        Forms\Components\Hidden::make('created_by')
                            ->default(fn() => Auth::id()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('শিরোনাম')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->label('ধরণ')
                    ->badge()
                    ->formatStateUsing(fn($state) => Notice::typeOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'urgent' => 'danger',
                        'exam' => 'warning',
                        'academic' => 'info',
                        'event' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('audience')
                    ->label('প্রাপক')
                    ->formatStateUsing(fn($state) => Notice::audienceOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('publish_date')
                    ->label('প্রকাশ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('মেয়াদ')
                    ->date('d M Y')
                    ->placeholder('চলমান'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('প্রকাশিত')
                    ->boolean(),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('তৈরি করেছেন')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('ধরণ')
                    ->options(Notice::typeOptions()),

                Tables\Filters\SelectFilter::make('audience')
                    ->label('প্রাপক')
                    ->options(Notice::audienceOptions()),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('প্রকাশিত'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('publish_date', 'desc')
            ->emptyStateHeading('কোন নোটিশ নেই')
            ->emptyStateIcon('heroicon-o-megaphone');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotices::route('/'),
            'create' => Pages\CreateNotice::route('/create'),
            'edit' => Pages\EditNotice::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('type', 'urgent')
            ->where('is_published', true)
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
