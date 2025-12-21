<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncomeResource\Pages;
use App\Models\Income;
use App\Models\IncomeHead;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IncomeResource extends BaseResource
{
    protected static ?string $model = Income::class;

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'আয়';

    protected static ?string $pluralModelLabel = 'আয়সমূহ';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('আয় এন্ট্রি')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('income_head_id')
                                    ->label('আয়ের খাত')
                                    ->relationship('incomeHead', 'name', fn(Builder $query) => $query->active())
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\TextInput::make('title')
                                    ->label('বিবরণ')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->label('পরিমাণ')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->required(),

                                Forms\Components\DatePicker::make('date')
                                    ->label('তারিখ')
                                    ->default(now())
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('payment_method')
                                    ->label('মাধ্যম')
                                    ->options(Income::paymentMethodOptions())
                                    ->default('cash')
                                    ->required()
                                    ->native(false)
                                    ->live(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('bank_account_id')
                                    ->label('ব্যাংক হিসাব')
                                    ->relationship('bankAccount', 'name', fn(Builder $query) => $query->active())
                                    ->native(false)
                                    ->preload()
                                    ->visible(fn(Forms\Get $get) => $get('payment_method') === 'bank'),

                                Forms\Components\TextInput::make('reference_no')
                                    ->label('রেফারেন্স নং'),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('বিস্তারিত বিবরণ')
                            ->rows(2),

                        Forms\Components\FileUpload::make('attachment')
                            ->label('সংযুক্তি')
                            ->directory('incomes')
                            ->acceptedFileTypes(['application/pdf', 'image/*']),

                        Forms\Components\Hidden::make('created_by')
                            ->default(fn() => auth()->id()),
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

                Tables\Columns\TextColumn::make('incomeHead.name')
                    ->label('খাত')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('title')
                    ->label('বিবরণ')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('amount')
                    ->label('পরিমাণ')
                    ->money('BDT')
                    ->color('success')
                    ->weight('bold')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('মাধ্যম')
                    ->formatStateUsing(fn($state) => Income::paymentMethodOptions()[$state] ?? $state)
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('এন্ট্রি করেছেন')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('income_head_id')
                    ->label('খাত')
                    ->relationship('incomeHead', 'name')
                    ->preload(),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('থেকে'),
                        Forms\Components\DatePicker::make('until')->label('পর্যন্ত'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('date', 'desc')
            ->emptyStateHeading('কোন আয় এন্ট্রি নেই')
            ->emptyStateIcon('heroicon-o-plus-circle');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncomes::route('/'),
            'create' => Pages\CreateIncome::route('/create'),
            'edit' => Pages\EditIncome::route('/{record}/edit'),
        ];
    }
}
