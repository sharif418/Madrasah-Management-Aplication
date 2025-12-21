<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpenseResource extends BaseResource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-minus-circle';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'ব্যয়';

    protected static ?string $pluralModelLabel = 'ব্যয়সমূহ';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ব্যয় এন্ট্রি')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('expense_head_id')
                                    ->label('ব্যয়ের খাত')
                                    ->relationship('expenseHead', 'name', fn(Builder $query) => $query->active())
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

                                Forms\Components\TextInput::make('vendor')
                                    ->label('বিক্রেতা/সরবরাহকারী'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('payment_method')
                                    ->label('মাধ্যম')
                                    ->options(Expense::paymentMethodOptions())
                                    ->default('cash')
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                Forms\Components\Select::make('bank_account_id')
                                    ->label('ব্যাংক হিসাব')
                                    ->relationship('bankAccount', 'name', fn(Builder $query) => $query->active())
                                    ->native(false)
                                    ->preload()
                                    ->visible(fn(Forms\Get $get) => $get('payment_method') === 'bank'),

                                Forms\Components\TextInput::make('invoice_no')
                                    ->label('ইনভয়েস নং'),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('বিস্তারিত বিবরণ')
                            ->rows(2),

                        Forms\Components\FileUpload::make('attachment')
                            ->label('সংযুক্তি/রসিদ')
                            ->directory('expenses')
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

                Tables\Columns\TextColumn::make('expenseHead.name')
                    ->label('খাত')
                    ->badge()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('title')
                    ->label('বিবরণ')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('vendor')
                    ->label('বিক্রেতা')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('পরিমাণ')
                    ->money('BDT')
                    ->color('danger')
                    ->weight('bold')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('মাধ্যম')
                    ->formatStateUsing(fn($state) => Expense::paymentMethodOptions()[$state] ?? $state)
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('expense_head_id')
                    ->label('খাত')
                    ->relationship('expenseHead', 'name')
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
            ->emptyStateHeading('কোন ব্যয় এন্ট্রি নেই')
            ->emptyStateIcon('heroicon-o-minus-circle');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
