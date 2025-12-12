<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HostelResource\Pages;
use App\Models\Hostel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HostelResource extends Resource
{
    protected static ?string $model = Hostel::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'হোস্টেল ও পরিবহন';

    protected static ?string $modelLabel = 'হোস্টেল';

    protected static ?string $pluralModelLabel = 'হোস্টেলসমূহ';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('হোস্টেল তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('হোস্টেলের নাম')
                                    ->placeholder('ছাত্র হোস্টেল-১')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Select::make('type')
                                    ->label('ধরণ')
                                    ->options(Hostel::typeOptions())
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Textarea::make('address')
                            ->label('ঠিকানা')
                            ->rows(2),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('warden_name')
                                    ->label('ওয়ার্ডেনের নাম'),

                                Forms\Components\TextInput::make('warden_phone')
                                    ->label('ওয়ার্ডেনের ফোন')
                                    ->tel(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('total_rooms')
                                    ->label('মোট রুম')
                                    ->numeric()
                                    ->default(0),

                                Forms\Components\TextInput::make('total_beds')
                                    ->label('মোট বেড')
                                    ->numeric()
                                    ->default(0),
                            ]),

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
                    ->label('হোস্টেলের নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->label('ধরণ')
                    ->badge()
                    ->formatStateUsing(fn($state) => Hostel::typeOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'boys' => 'info',
                        'girls' => 'success',
                        'mixed' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('warden_name')
                    ->label('ওয়ার্ডেন')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_rooms')
                    ->label('রুম')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('total_beds')
                    ->label('বেড')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('available_beds')
                    ->label('খালি')
                    ->badge()
                    ->color('success')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateHeading('কোন হোস্টেল নেই')
            ->emptyStateIcon('heroicon-o-home-modern');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHostels::route('/'),
            'create' => Pages\CreateHostel::route('/create'),
            'edit' => Pages\EditHostel::route('/{record}/edit'),
        ];
    }
}
