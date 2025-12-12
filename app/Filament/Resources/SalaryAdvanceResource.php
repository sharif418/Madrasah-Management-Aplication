<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaryAdvanceResource\Pages;
use App\Models\SalaryAdvance;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class SalaryAdvanceResource extends Resource
{
    protected static ?string $model = SalaryAdvance::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'অগ্রিম বেতন';

    protected static ?string $modelLabel = 'অগ্রিম বেতন';

    protected static ?string $pluralModelLabel = 'অগ্রিম বেতন';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('অগ্রিম তথ্য')
                    ->description('কর্মীর অগ্রিম বেতনের আবেদন')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('staff_id')
                                    ->label('কর্মী নির্বাচন')
                                    ->relationship('staff', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\TextInput::make('amount')
                                    ->label('অগ্রিমের পরিমাণ')
                                    ->prefix('৳')
                                    ->numeric()
                                    ->required()
                                    ->minValue(100)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                        $months = $get('deduction_months') ?: 1;
                                        $set('monthly_deduction', round($state / $months, 2));
                                    }),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('advance_date')
                                    ->label('তারিখ')
                                    ->required()
                                    ->default(now())
                                    ->native(false),

                                Forms\Components\TextInput::make('deduction_months')
                                    ->label('কত মাসে কাটবে')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->maxValue(12)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                        $amount = $get('amount') ?: 0;
                                        $set('monthly_deduction', round($amount / max(1, $state), 2));
                                    }),

                                Forms\Components\TextInput::make('monthly_deduction')
                                    ->label('মাসিক কর্তন')
                                    ->prefix('৳')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(),
                            ]),

                        Forms\Components\Textarea::make('reason')
                            ->label('কারণ')
                            ->rows(2)
                            ->placeholder('অগ্রিম নেওয়ার কারণ...')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->label('স্ট্যাটাস')
                            ->options(SalaryAdvance::getStatusOptions())
                            ->default('pending')
                            ->native(false)
                            ->disabled(fn($record) => $record === null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('advance_no')
                    ->label('রেফ নং')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('staff.name')
                    ->label('কর্মীর নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('staff.designation')
                    ->label('পদবি')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('পরিমাণ')
                    ->money('BDT')
                    ->alignEnd()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('deducted_amount')
                    ->label('কর্তন হয়েছে')
                    ->money('BDT')
                    ->alignEnd()
                    ->color('success'),

                Tables\Columns\TextColumn::make('remaining_amount')
                    ->label('বাকি')
                    ->money('BDT')
                    ->alignEnd()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('deduction_months')
                    ->label('মাস')
                    ->formatStateUsing(fn($state) => $state . ' মাস')
                    ->badge(),

                Tables\Columns\TextColumn::make('advance_date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => SalaryAdvance::getStatusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'paid' => 'primary',
                        'deducting' => 'success',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(SalaryAdvance::getStatusOptions()),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('অনুমোদন')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (SalaryAdvance $record) {
                        $record->approve(auth()->id());
                        Notification::make()->success()->title('অনুমোদিত!')->send();
                    }),

                Tables\Actions\Action::make('pay')
                    ->label('প্রদান')
                    ->icon('heroicon-o-banknotes')
                    ->color('primary')
                    ->visible(fn($record) => $record->status === 'approved')
                    ->requiresConfirmation()
                    ->action(function (SalaryAdvance $record) {
                        $record->markAsPaid();
                        Notification::make()->success()->title('প্রদান করা হয়েছে!')->send();
                    }),

                Tables\Actions\Action::make('deduct')
                    ->label('কর্তন')
                    ->icon('heroicon-o-minus-circle')
                    ->color('warning')
                    ->visible(fn($record) => in_array($record->status, ['paid', 'deducting']) && $record->remaining_amount > 0)
                    ->form([
                        Forms\Components\TextInput::make('deduct_amount')
                            ->label('কর্তনের পরিমাণ')
                            ->prefix('৳')
                            ->numeric()
                            ->required()
                            ->default(fn($record) => min($record->monthly_deduction, $record->remaining_amount)),
                    ])
                    ->action(function (SalaryAdvance $record, array $data) {
                        $record->deduct($data['deduct_amount']);
                        Notification::make()->success()->title('কর্তন সম্পন্ন!')->send();
                    }),

                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\ViewAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalaryAdvances::route('/'),
            'create' => Pages\CreateSalaryAdvance::route('/create'),
            'edit' => Pages\EditSalaryAdvance::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
