<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffLoanResource\Pages;
use App\Models\StaffLoan;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class StaffLoanResource extends BaseResource
{
    protected static ?string $model = StaffLoan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'কর্মী ঋণ';

    protected static ?string $modelLabel = 'ঋণ';

    protected static ?string $pluralModelLabel = 'কর্মী ঋণ';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ঋণের তথ্য')
                    ->description('কর্মীকে ঋণ প্রদান')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('staff_id')
                                    ->label('কর্মী নির্বাচন')
                                    ->relationship('staff', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\TextInput::make('loan_amount')
                                    ->label('ঋণের পরিমাণ')
                                    ->prefix('৳')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1000)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                        $installments = $get('total_installments') ?: 1;
                                        $set('monthly_deduction', round($state / $installments, 2));
                                    }),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('loan_date')
                                    ->label('ঋণের তারিখ')
                                    ->required()
                                    ->default(now())
                                    ->native(false),

                                Forms\Components\TextInput::make('total_installments')
                                    ->label('মোট কিস্তি')
                                    ->numeric()
                                    ->default(12)
                                    ->minValue(1)
                                    ->maxValue(60)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                        $amount = $get('loan_amount') ?: 0;
                                        $set('monthly_deduction', round($amount / max(1, $state), 2));
                                    }),

                                Forms\Components\TextInput::make('monthly_deduction')
                                    ->label('মাসিক কর্তন')
                                    ->prefix('৳')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(),
                            ]),

                        Forms\Components\DatePicker::make('start_deduction_date')
                            ->label('কর্তন শুরুর তারিখ')
                            ->required()
                            ->default(now()->addMonth()->startOfMonth())
                            ->native(false),

                        Forms\Components\Textarea::make('purpose')
                            ->label('ঋণের উদ্দেশ্য')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->label('স্ট্যাটাস')
                            ->options(StaffLoan::getStatusOptions())
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
                Tables\Columns\TextColumn::make('loan_no')
                    ->label('রেফ নং')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('staff.name')
                    ->label('কর্মীর নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('loan_amount')
                    ->label('ঋণ')
                    ->money('BDT')
                    ->alignEnd()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('পরিশোধ')
                    ->money('BDT')
                    ->alignEnd()
                    ->color('success'),

                Tables\Columns\TextColumn::make('remaining_amount')
                    ->label('বাকি')
                    ->money('BDT')
                    ->alignEnd()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('total_installments')
                    ->label('কিস্তি')
                    ->formatStateUsing(fn($record) => $record->remaining_installments . '/' . $record->total_installments),

                Tables\Columns\TextColumn::make('monthly_deduction')
                    ->label('মাসিক')
                    ->money('BDT'),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => StaffLoan::getStatusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'active' => 'success',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(StaffLoan::getStatusOptions()),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('অনুমোদন')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (StaffLoan $record) {
                        $record->approve(auth()->id());
                        $record->activate();
                        Notification::make()->success()->title('ঋণ অনুমোদিত ও সক্রিয়!')->send();
                    }),

                Tables\Actions\Action::make('deduct')
                    ->label('কিস্তি কর্তন')
                    ->icon('heroicon-o-minus-circle')
                    ->color('warning')
                    ->visible(fn($record) => $record->status === 'active' && $record->remaining_amount > 0)
                    ->form([
                        Forms\Components\TextInput::make('deduct_amount')
                            ->label('কর্তনের পরিমাণ')
                            ->prefix('৳')
                            ->numeric()
                            ->required()
                            ->default(fn($record) => min($record->monthly_deduction, $record->remaining_amount)),
                    ])
                    ->action(function (StaffLoan $record, array $data) {
                        $record->deduct($data['deduct_amount']);
                        Notification::make()->success()->title('কিস্তি কর্তন সম্পন্ন!')->send();
                    }),

                Tables\Actions\EditAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaffLoans::route('/'),
            'create' => Pages\CreateStaffLoan::route('/create'),
            'edit' => Pages\EditStaffLoan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'active')->count();
        return $count > 0 ? (string) $count : null;
    }
}
