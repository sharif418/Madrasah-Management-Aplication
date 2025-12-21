<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetResource\Pages;
use App\Models\Budget;
use App\Models\IncomeHead;
use App\Models\ExpenseHead;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class BudgetResource extends BaseResource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'বাজেট পরিকল্পনা';

    protected static ?string $modelLabel = 'বাজেট';

    protected static ?string $pluralModelLabel = 'বাজেট পরিকল্পনা';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('বাজেট তথ্য')
                    ->description('হেড অনুযায়ী বাজেট সেট করুন')
                    ->icon('heroicon-o-calculator')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('fiscal_year')
                                    ->label('অর্থ বছর')
                                    ->options(Budget::getFiscalYearOptions())
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('type')
                                    ->label('ধরণ')
                                    ->options([
                                        'income' => 'আয়',
                                        'expense' => 'ব্যয়',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('income_head_id', null);
                                        $set('expense_head_id', null);
                                    }),

                                Forms\Components\Select::make('income_head_id')
                                    ->label('আয় হেড')
                                    ->options(IncomeHead::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->native(false)
                                    ->visible(fn(Forms\Get $get) => $get('type') === 'income')
                                    ->required(fn(Forms\Get $get) => $get('type') === 'income'),

                                Forms\Components\Select::make('expense_head_id')
                                    ->label('ব্যয় হেড')
                                    ->options(ExpenseHead::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->native(false)
                                    ->visible(fn(Forms\Get $get) => $get('type') === 'expense')
                                    ->required(fn(Forms\Get $get) => $get('type') === 'expense'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('budgeted_amount')
                                    ->label('বাজেটকৃত পরিমাণ')
                                    ->prefix('৳')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0),

                                Forms\Components\TextInput::make('actual_amount')
                                    ->label('প্রকৃত পরিমাণ')
                                    ->prefix('৳')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled(),
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->label('নোট')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fiscal_year')
                    ->label('অর্থ বছর')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('ধরণ')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state === 'income' ? 'আয়' : 'ব্যয়')
                    ->color(fn($state) => $state === 'income' ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('head_name')
                    ->label('হেড')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('budgeted_amount')
                    ->label('বাজেট')
                    ->money('BDT')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('actual_amount')
                    ->label('প্রকৃত')
                    ->money('BDT')
                    ->alignEnd()
                    ->color(fn($record) => $record->is_over_budget ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('variance')
                    ->label('পার্থক্য')
                    ->money('BDT')
                    ->alignEnd()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => ($state >= 0 ? '+' : '') . '৳' . number_format($state, 0)),

                Tables\Columns\TextColumn::make('variance_percentage')
                    ->label('%')
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->badge()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('fiscal_year')
                    ->label('অর্থ বছর')
                    ->options(Budget::getFiscalYearOptions()),

                Tables\Filters\SelectFilter::make('type')
                    ->label('ধরণ')
                    ->options([
                        'income' => 'আয়',
                        'expense' => 'ব্যয়',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fiscal_year', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
        ];
    }
}
