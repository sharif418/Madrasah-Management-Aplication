<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FuelLogResource\Pages;
use App\Models\FuelLog;
use App\Models\Vehicle;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FuelLogResource extends Resource
{
    protected static ?string $model = FuelLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationGroup = 'হোস্টেল ও পরিবহন';

    protected static ?string $navigationLabel = 'জ্বালানি লগ';

    protected static ?string $modelLabel = 'জ্বালানি';

    protected static ?string $pluralModelLabel = 'জ্বালানি লগ';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('জ্বালানি তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('vehicle_id')
                                    ->label('গাড়ি')
                                    ->options(Vehicle::where('status', 'active')
                                        ->get()
                                        ->mapWithKeys(fn($v) => [$v->id => "{$v->vehicle_no} - {$v->type}"]))
                                    ->required()
                                    ->searchable()
                                    ->native(false),

                                Forms\Components\Select::make('driver_id')
                                    ->label('ড্রাইভার')
                                    ->options(Staff::where('status', 'active')
                                        ->whereHas('designation', fn($q) => $q->where('title', 'like', '%ড্রাইভার%'))
                                        ->orWhere('status', 'active')
                                        ->pluck('name', 'id'))
                                    ->searchable()
                                    ->native(false),

                                Forms\Components\DatePicker::make('date')
                                    ->label('তারিখ')
                                    ->required()
                                    ->default(now())
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Select::make('fuel_type')
                                    ->label('জ্বালানি ধরণ')
                                    ->options(FuelLog::fuelTypeOptions())
                                    ->default('diesel')
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('পরিমাণ (লিটার)')
                                    ->numeric()
                                    ->required()
                                    ->suffix('লি.')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $rate = $get('rate') ?? 0;
                                        $set('total_cost', round($state * $rate, 2));
                                    }),

                                Forms\Components\TextInput::make('rate')
                                    ->label('দর (প্রতি লিটার)')
                                    ->numeric()
                                    ->required()
                                    ->prefix('৳')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $qty = $get('quantity') ?? 0;
                                        $set('total_cost', round($qty * $state, 2));
                                    }),

                                Forms\Components\TextInput::make('total_cost')
                                    ->label('মোট খরচ')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                    ]),

                Forms\Components\Section::make('অতিরিক্ত তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('odometer_reading')
                                    ->label('ওডোমিটার (কিমি)')
                                    ->numeric(),

                                Forms\Components\TextInput::make('fuel_station')
                                    ->label('পাম্প/স্টেশন'),

                                Forms\Components\TextInput::make('receipt_no')
                                    ->label('রসিদ নং'),
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->label('মন্তব্য')
                            ->rows(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('vehicle.vehicle_no')
                    ->label('গাড়ি')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('fuel_type')
                    ->label('ধরণ')
                    ->formatStateUsing(fn($state) => FuelLog::fuelTypeOptions()[$state] ?? $state)
                    ->badge(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('লিটার')
                    ->numeric(2)
                    ->suffix(' লি.'),

                Tables\Columns\TextColumn::make('rate')
                    ->label('দর')
                    ->money('BDT'),

                Tables\Columns\TextColumn::make('total_cost')
                    ->label('মোট')
                    ->money('BDT')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('odometer_reading')
                    ->label('কিমি')
                    ->numeric()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('mileage')
                    ->label('মাইলেজ')
                    ->suffix(' কিমি/লি.')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('driver.name')
                    ->label('ড্রাইভার')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('গাড়ি')
                    ->options(Vehicle::pluck('vehicle_no', 'id')),

                Tables\Filters\SelectFilter::make('fuel_type')
                    ->label('জ্বালানি')
                    ->options(FuelLog::fuelTypeOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('কোন রেকর্ড নেই')
            ->emptyStateIcon('heroicon-o-fire');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFuelLogs::route('/'),
            'create' => Pages\CreateFuelLog::route('/create'),
            'edit' => Pages\EditFuelLog::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $total = static::getModel()::thisMonth()->sum('total_cost');
        return $total > 0 ? '৳' . number_format($total, 0) : null;
    }
}
