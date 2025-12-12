<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransportRouteResource\Pages;
use App\Models\TransportRoute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransportRouteResource extends Resource
{
    protected static ?string $model = TransportRoute::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'হোস্টেল ও পরিবহন';

    protected static ?string $modelLabel = 'পরিবহন রুট';

    protected static ?string $pluralModelLabel = 'পরিবহন রুটসমূহ';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('রুট তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('রুটের নাম')
                                    ->placeholder('রুট-১ (মিরপুর)')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Select::make('vehicle_id')
                                    ->label('যানবাহন')
                                    ->relationship('vehicle', 'vehicle_no', fn(Builder $query) => $query->active())
                                    ->native(false)
                                    ->preload()
                                    ->searchable(),
                            ]),

                        Forms\Components\TextInput::make('monthly_fee')
                            ->label('মাসিক ভাড়া')
                            ->numeric()
                            ->prefix('৳')
                            ->default(0),

                        Forms\Components\Repeater::make('stops')
                            ->label('স্টপেজসমূহ')
                            ->simple(
                                Forms\Components\TextInput::make('stop')
                                    ->placeholder('স্টপের নাম')
                            )
                            ->addActionLabel('স্টপ যোগ করুন')
                            ->reorderable()
                            ->collapsible(),

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
                    ->label('রুটের নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('vehicle.vehicle_no')
                    ->label('যানবাহন')
                    ->badge()
                    ->color('primary')
                    ->placeholder('নির্ধারণ করা হয়নি'),

                Tables\Columns\TextColumn::make('monthly_fee')
                    ->label('মাসিক ভাড়া')
                    ->money('BDT'),

                Tables\Columns\TextColumn::make('student_count')
                    ->label('ছাত্র সংখ্যা')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('stops')
                    ->label('স্টপ সংখ্যা')
                    ->formatStateUsing(fn($state) => is_array($state) ? count($state) . ' টি' : '0 টি'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateHeading('কোন রুট নেই')
            ->emptyStateIcon('heroicon-o-map');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransportRoutes::route('/'),
            'create' => Pages\CreateTransportRoute::route('/create'),
            'edit' => Pages\EditTransportRoute::route('/{record}/edit'),
        ];
    }
}
