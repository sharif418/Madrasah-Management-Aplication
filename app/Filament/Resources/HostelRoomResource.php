<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HostelRoomResource\Pages;
use App\Models\HostelRoom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HostelRoomResource extends Resource
{
    protected static ?string $model = HostelRoom::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'হোস্টেল ও পরিবহন';

    protected static ?string $modelLabel = 'হোস্টেল রুম';

    protected static ?string $pluralModelLabel = 'হোস্টেল রুমসমূহ';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('রুম তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('hostel_id')
                                    ->label('হোস্টেল')
                                    ->relationship('hostel', 'name', fn(Builder $query) => $query->active())
                                    ->required()
                                    ->native(false)
                                    ->preload(),

                                Forms\Components\TextInput::make('room_no')
                                    ->label('রুম নং')
                                    ->required()
                                    ->maxLength(50),

                                Forms\Components\Select::make('type')
                                    ->label('ধরণ')
                                    ->options(HostelRoom::typeOptions())
                                    ->default('double')
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('capacity')
                                    ->label('ধারণক্ষমতা')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1),

                                Forms\Components\TextInput::make('monthly_rent')
                                    ->label('মাসিক ভাড়া')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0),

                                Forms\Components\TextInput::make('floor')
                                    ->label('ফ্লোর')
                                    ->numeric(),
                            ]),

                        Forms\Components\Textarea::make('facilities')
                            ->label('সুবিধাসমূহ')
                            ->placeholder('এসি, বাথরুম, বারান্দা ইত্যাদি')
                            ->rows(2),

                        Forms\Components\Select::make('status')
                            ->label('স্ট্যাটাস')
                            ->options(HostelRoom::statusOptions())
                            ->default('available')
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hostel.name')
                    ->label('হোস্টেল')
                    ->sortable(),

                Tables\Columns\TextColumn::make('room_no')
                    ->label('রুম নং')
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('ধরণ')
                    ->formatStateUsing(fn($state) => HostelRoom::typeOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('floor')
                    ->label('ফ্লোর')
                    ->suffix(' তলা')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('ধারণক্ষমতা')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('occupied_beds')
                    ->label('ভর্তি')
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('available_beds')
                    ->label('খালি')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('monthly_rent')
                    ->label('ভাড়া')
                    ->money('BDT'),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => HostelRoom::statusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'available' => 'success',
                        'full' => 'danger',
                        'maintenance' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hostel_id')
                    ->label('হোস্টেল')
                    ->relationship('hostel', 'name')
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(HostelRoom::statusOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateHeading('কোন রুম নেই')
            ->emptyStateIcon('heroicon-o-key');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHostelRooms::route('/'),
            'create' => Pages\CreateHostelRoom::route('/create'),
            'edit' => Pages\EditHostelRoom::route('/{record}/edit'),
        ];
    }
}
