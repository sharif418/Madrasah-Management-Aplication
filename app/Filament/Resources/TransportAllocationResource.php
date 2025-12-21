<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransportAllocationResource\Pages;
use App\Models\TransportAllocation;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class TransportAllocationResource extends BaseResource
{
    protected static ?string $model = TransportAllocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'হোস্টেল ও পরিবহন';

    protected static ?string $modelLabel = 'পরিবহন বরাদ্দ';

    protected static ?string $pluralModelLabel = 'পরিবহন বরাদ্দসমূহ';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('পরিবহন বরাদ্দ')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('ছাত্র')
                            ->relationship('student', 'name', fn(Builder $query) => $query->where('status', 'active'))
                            ->required()
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name_bn} ({$record->student_id})"),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('transport_route_id')
                                    ->label('রুট')
                                    ->relationship('transportRoute', 'name', fn(Builder $query) => $query->active())
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} (৳{$record->monthly_fee}/মাস)"),

                                Forms\Components\TextInput::make('pickup_point')
                                    ->label('পিকআপ পয়েন্ট')
                                    ->placeholder('স্টপের নাম'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('শুরুর তারিখ')
                                    ->default(now())
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(TransportAllocation::statusOptions())
                                    ->default('active')
                                    ->native(false)
                                    ->disabled(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('ছাত্র')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn($record) => $record->student?->student_id),

                Tables\Columns\TextColumn::make('transportRoute.name')
                    ->label('রুট')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('pickup_point')
                    ->label('পিকআপ পয়েন্ট')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('transportRoute.monthly_fee')
                    ->label('মাসিক ভাড়া')
                    ->money('BDT'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('শুরু')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => TransportAllocation::statusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('transport_route_id')
                    ->label('রুট')
                    ->relationship('transportRoute', 'name')
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(TransportAllocation::statusOptions()),
            ])
            ->actions([
                Tables\Actions\Action::make('deactivate')
                    ->label('বন্ধ করুন')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (TransportAllocation $record): void {
                        $record->deactivate();

                        Notification::make()
                            ->success()
                            ->title('পরিবহন সেবা বন্ধ করা হয়েছে')
                            ->send();
                    })
                    ->visible(fn(TransportAllocation $record): bool => $record->status === 'active'),

                Tables\Actions\EditAction::make()
                    ->visible(fn(TransportAllocation $record): bool => $record->status === 'active'),
            ])
            ->emptyStateHeading('কোন বরাদ্দ নেই')
            ->emptyStateIcon('heroicon-o-ticket');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransportAllocations::route('/'),
            'create' => Pages\CreateTransportAllocation::route('/create'),
            'edit' => Pages\EditTransportAllocation::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count() ?: null;
    }
}
