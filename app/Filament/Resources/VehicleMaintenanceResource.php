<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleMaintenanceResource\Pages;
use App\Models\VehicleMaintenance;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleMaintenanceResource extends BaseResource
{
    protected static ?string $model = VehicleMaintenance::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'হোস্টেল ও পরিবহন';

    protected static ?string $navigationLabel = 'মেইনটেন্যান্স';

    protected static ?string $modelLabel = 'মেইনটেন্যান্স';

    protected static ?string $pluralModelLabel = 'মেইনটেন্যান্স';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('মেইনটেন্যান্স তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('vehicle_id')
                                    ->label('গাড়ি')
                                    ->options(Vehicle::where('status', 'active')
                                        ->get()
                                        ->mapWithKeys(fn($v) => [$v->id => "{$v->vehicle_no} - {$v->type}"]))
                                    ->required()
                                    ->searchable()
                                    ->native(false),

                                Forms\Components\Select::make('maintenance_type')
                                    ->label('ধরণ')
                                    ->options(VehicleMaintenance::typeOptions())
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('maintenance_date')
                                    ->label('তারিখ')
                                    ->required()
                                    ->default(now())
                                    ->native(false),

                                Forms\Components\DatePicker::make('next_maintenance_date')
                                    ->label('পরবর্তী তারিখ')
                                    ->native(false),

                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(VehicleMaintenance::statusOptions())
                                    ->default('completed')
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('বিবরণ')
                            ->rows(2),
                    ]),

                Forms\Components\Section::make('খরচ ও অন্যান্য')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('cost')
                                    ->label('খরচ')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0),

                                Forms\Components\TextInput::make('odometer_reading')
                                    ->label('ওডোমিটার (কিমি)')
                                    ->numeric(),

                                Forms\Components\TextInput::make('service_provider')
                                    ->label('সার্ভিস প্রদানকারী'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('invoice_no')
                                    ->label('ইনভয়েস নং'),

                                Forms\Components\Textarea::make('notes')
                                    ->label('মন্তব্য')
                                    ->rows(1),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('maintenance_date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('vehicle.vehicle_no')
                    ->label('গাড়ি')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('maintenance_type')
                    ->label('ধরণ')
                    ->formatStateUsing(fn($state) => VehicleMaintenance::typeOptions()[$state] ?? $state)
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('cost')
                    ->label('খরচ')
                    ->money('BDT')
                    ->sortable(),

                Tables\Columns\TextColumn::make('odometer_reading')
                    ->label('কিমি')
                    ->numeric()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->formatStateUsing(fn($state) => VehicleMaintenance::statusOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'completed' => 'success',
                        'in_progress' => 'warning',
                        'scheduled' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('next_maintenance_date')
                    ->label('পরবর্তী')
                    ->date('d M Y')
                    ->toggleable(),
            ])
            ->defaultSort('maintenance_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('গাড়ি')
                    ->options(Vehicle::pluck('vehicle_no', 'id')),

                Tables\Filters\SelectFilter::make('maintenance_type')
                    ->label('ধরণ')
                    ->options(VehicleMaintenance::typeOptions()),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(VehicleMaintenance::statusOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('কোন রেকর্ড নেই')
            ->emptyStateIcon('heroicon-o-wrench-screwdriver');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicleMaintenances::route('/'),
            'create' => Pages\CreateVehicleMaintenance::route('/create'),
            'edit' => Pages\EditVehicleMaintenance::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::thisMonth()->count() ?: null;
    }
}
