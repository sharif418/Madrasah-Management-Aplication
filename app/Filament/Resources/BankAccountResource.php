<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankAccountResource\Pages;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class BankAccountResource extends BaseResource
{
    protected static ?string $model = BankAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'ব্যাংক হিসাব';

    protected static ?string $pluralModelLabel = 'ব্যাংক হিসাবসমূহ';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ব্যাংক হিসাব')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('হিসাবের নাম')
                                    ->placeholder('যেমন: চলতি হিসাব, সঞ্চয়ী হিসাব')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('bank_name')
                                    ->label('ব্যাংকের নাম')
                                    ->placeholder('যেমন: ইসলামী ব্যাংক')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('account_number')
                                    ->label('হিসাব নম্বর')
                                    ->required()
                                    ->maxLength(50),

                                Forms\Components\TextInput::make('branch')
                                    ->label('শাখা')
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('opening_balance')
                                    ->label('প্রারম্ভিক ব্যালেন্স')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0),

                                Forms\Components\TextInput::make('current_balance')
                                    ->label('বর্তমান ব্যালেন্স')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated(false),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('হিসাবের নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('bank_name')
                    ->label('ব্যাংক')
                    ->searchable(),

                Tables\Columns\TextColumn::make('account_number')
                    ->label('হিসাব নম্বর')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('branch')
                    ->label('শাখা')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('current_balance')
                    ->label('বর্তমান ব্যালেন্স')
                    ->money('BDT')
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->weight('bold'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateHeading('কোন ব্যাংক হিসাব নেই')
            ->emptyStateIcon('heroicon-o-building-library');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}
