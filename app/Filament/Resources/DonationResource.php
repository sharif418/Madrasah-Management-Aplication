<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationResource\Pages;
use App\Models\Donation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'দান-অনুদান';

    protected static ?string $pluralModelLabel = 'দান-অনুদানসমূহ';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('দাতার তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('donor_name')
                                    ->label('দাতার নাম')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('donor_phone')
                                    ->label('ফোন')
                                    ->tel()
                                    ->maxLength(20),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('donor_email')
                                    ->label('ইমেইল')
                                    ->email(),

                                Forms\Components\Textarea::make('donor_address')
                                    ->label('ঠিকানা')
                                    ->rows(2),
                            ]),
                    ]),

                Forms\Components\Section::make('দানের তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('fund_type')
                                    ->label('ফান্ডের ধরণ')
                                    ->options(Donation::fundTypeOptions())
                                    ->default('general')
                                    ->required()
                                    ->native(false),

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
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('payment_method')
                                    ->label('মাধ্যম')
                                    ->options(Donation::paymentMethodOptions())
                                    ->default('cash')
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('transaction_id')
                                    ->label('ট্রান্সাকশন আইডি'),
                            ]),

                        Forms\Components\Textarea::make('purpose')
                            ->label('দানের উদ্দেশ্য')
                            ->rows(2),

                        Forms\Components\Textarea::make('remarks')
                            ->label('মন্তব্য')
                            ->rows(2),

                        Forms\Components\Hidden::make('received_by')
                            ->default(fn() => auth()->id()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_no')
                    ->label('রসিদ নং')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('donor_name')
                    ->label('দাতার নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('fund_type')
                    ->label('ফান্ড')
                    ->badge()
                    ->formatStateUsing(fn($state) => Donation::fundTypeOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'zakat' => 'success',
                        'sadaqah' => 'info',
                        'lillah' => 'warning',
                        'building' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->label('পরিমাণ')
                    ->money('BDT')
                    ->color('success')
                    ->weight('bold')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('মাধ্যম')
                    ->formatStateUsing(fn($state) => Donation::paymentMethodOptions()[$state] ?? $state)
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('fund_type')
                    ->label('ফান্ড')
                    ->options(Donation::fundTypeOptions()),

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
                Tables\Actions\Action::make('print')
                    ->label('রসিদ')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('donation.receipt', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('date', 'desc')
            ->emptyStateHeading('কোন দান-অনুদান নেই')
            ->emptyStateDescription('নতুন দান এন্ট্রি করুন')
            ->emptyStateIcon('heroicon-o-heart');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonations::route('/'),
            'create' => Pages\CreateDonation::route('/create'),
            'edit' => Pages\EditDonation::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $thisMonth = static::getModel()::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();
        return $thisMonth ?: null;
    }
}
