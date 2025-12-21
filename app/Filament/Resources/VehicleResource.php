<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleResource extends BaseResource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'হোস্টেল ও পরিবহন';

    protected static ?string $modelLabel = 'যানবাহন';

    protected static ?string $pluralModelLabel = 'যানবাহনসমূহ';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('যানবাহন তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('vehicle_no')
                                    ->label('গাড়ি নম্বর')
                                    ->placeholder('ঢাকা মেট্রো-গ-১২৩৪')
                                    ->required()
                                    ->maxLength(50),

                                Forms\Components\Select::make('vehicle_type')
                                    ->label('গাড়ির ধরণ')
                                    ->options(Vehicle::typeOptions())
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('capacity')
                                    ->label('ধারণক্ষমতা')
                                    ->numeric()
                                    ->suffix('জন')
                                    ->required(),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('driver_name')
                                    ->label('ড্রাইভারের নাম'),

                                Forms\Components\TextInput::make('driver_phone')
                                    ->label('ড্রাইভারের ফোন')
                                    ->tel(),

                                Forms\Components\TextInput::make('driver_license')
                                    ->label('লাইসেন্স নং'),
                            ]),

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
                Tables\Columns\TextColumn::make('vehicle_no')
                    ->label('গাড়ি নম্বর')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('vehicle_type')
                    ->label('ধরণ'),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('ধারণক্ষমতা')
                    ->suffix(' জন')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('driver_name')
                    ->label('ড্রাইভার')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('driver_phone')
                    ->label('ফোন'),

                Tables\Columns\TextColumn::make('routes_count')
                    ->label('রুট সংখ্যা')
                    ->counts('routes')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateHeading('কোন যানবাহন নেই')
            ->emptyStateIcon('heroicon-o-truck');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
