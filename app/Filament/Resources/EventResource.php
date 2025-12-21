<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class EventResource extends BaseResource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'যোগাযোগ';

    protected static ?string $modelLabel = 'ইভেন্ট';

    protected static ?string $pluralModelLabel = 'ইভেন্ট ক্যালেন্ডার';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ইভেন্ট তথ্য')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('শিরোনাম')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('বিবরণ')
                            ->rows(3),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('ধরণ')
                                    ->options(Event::typeOptions())
                                    ->default('academic')
                                    ->required()
                                    ->native(false),

                                Forms\Components\DatePicker::make('start_date')
                                    ->label('শুরু')
                                    ->required()
                                    ->native(false),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('শেষ')
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TimePicker::make('start_time')
                                    ->label('সময় শুরু')
                                    ->native(false),

                                Forms\Components\TimePicker::make('end_time')
                                    ->label('সময় শেষ')
                                    ->native(false),

                                Forms\Components\TextInput::make('venue')
                                    ->label('স্থান'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_holiday')
                                    ->label('ছুটির দিন')
                                    ->helperText('এই দিন মাদরাসা বন্ধ থাকবে'),

                                Forms\Components\Toggle::make('is_public')
                                    ->label('সবার জন্য দৃশ্যমান')
                                    ->default(true),
                            ]),

                        Forms\Components\FileUpload::make('image')
                            ->label('ছবি')
                            ->image()
                            ->directory('events'),
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
                    ->weight('bold')
                    ->limit(40),

                Tables\Columns\TextColumn::make('type')
                    ->label('ধরণ')
                    ->badge()
                    ->formatStateUsing(fn($state) => Event::typeOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'religious' => 'success',
                        'academic' => 'info',
                        'cultural' => 'warning',
                        'sports' => 'primary',
                        'meeting' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('venue')
                    ->label('স্থান')
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_holiday')
                    ->label('ছুটি')
                    ->boolean()
                    ->trueIcon('heroicon-o-sun')
                    ->trueColor('warning'),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('পাবলিক')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('ধরণ')
                    ->options(Event::typeOptions()),

                Tables\Filters\TernaryFilter::make('is_holiday')
                    ->label('ছুটি'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('start_date', 'desc')
            ->emptyStateHeading('কোন ইভেন্ট নেই')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('start_date', '>=', now()->toDateString())
            ->where('start_date', '<=', now()->addDays(7)->toDateString())
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
}
