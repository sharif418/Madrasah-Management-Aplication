<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeCollectionResource\Pages;
use App\Models\StudentFee;
use App\Models\FeePayment;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class FeeCollectionResource extends BaseResource
{
    protected static ?string $model = StudentFee::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'ফি ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'ফি আদায়';

    protected static ?string $pluralModelLabel = 'ফি আদায়';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'fee-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ছাত্রের ফি')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('student_id')
                                    ->label('ছাত্র')
                                    ->relationship('student', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('fee_structure_id')
                                    ->label('ফি কাঠামো')
                                    ->relationship('feeStructure', 'id')
                                    ->getOptionLabelFromRecordUsing(fn($record) => $record->feeType->name . ' - ৳' . number_format($record->amount, 2))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('original_amount')
                                    ->label('মূল পরিমাণ')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->required(),

                                Forms\Components\TextInput::make('discount_amount')
                                    ->label('ছাড়')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0),

                                Forms\Components\TextInput::make('final_amount')
                                    ->label('সর্বমোট')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->required(),

                                Forms\Components\TextInput::make('year')
                                    ->label('বছর')
                                    ->numeric()
                                    ->default(now()->year),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('ছাত্রের নাম')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->student?->admission_no),

                Tables\Columns\TextColumn::make('student.class.name')
                    ->label('শ্রেণি')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('feeStructure.feeType.name')
                    ->label('ফি এর ধরণ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('month')
                    ->label('মাস')
                    ->formatStateUsing(fn($state) => $state ? bengaliMonth($state) : '-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('final_amount')
                    ->label('মোট')
                    ->money('BDT')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('পরিশোধিত')
                    ->money('BDT')
                    ->color('success')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('due_amount')
                    ->label('বাকি')
                    ->money('BDT')
                    ->color('danger')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'pending' => 'gray',
                        'waived' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => StudentFee::statusOptions()[$state] ?? $state),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(StudentFee::statusOptions()),

                Tables\Filters\SelectFilter::make('class')
                    ->label('শ্রেণি')
                    ->relationship('student.class', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('collect')
                    ->label('আদায়')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('পরিমাণ (টাকা)')
                            ->numeric()
                            ->prefix('৳')
                            ->required()
                            ->default(fn($record) => $record->due_amount),

                        Forms\Components\Select::make('payment_method')
                            ->label('পেমেন্ট মাধ্যম')
                            ->options(FeePayment::paymentMethodOptions())
                            ->default('cash')
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('transaction_id')
                            ->label('ট্রান্সাকশন আইডি')
                            ->placeholder('বিকাশ/নগদ ট্রান্সাকশন আইডি'),

                        Forms\Components\Textarea::make('remarks')
                            ->label('মন্তব্য')
                            ->rows(2),
                    ])
                    ->action(function (StudentFee $record, array $data): void {
                        $payment = FeePayment::create([
                            'student_fee_id' => $record->id,
                            'student_id' => $record->student_id,
                            'amount' => $data['amount'],
                            'late_fee' => 0,
                            'total_amount' => $data['amount'],
                            'payment_method' => $data['payment_method'],
                            'transaction_id' => $data['transaction_id'] ?? null,
                            'remarks' => $data['remarks'] ?? null,
                            'collected_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('পেমেন্ট সফল!')
                            ->body("রসিদ নং: {$payment->receipt_no}")
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('print')
                                    ->label('রসিদ প্রিন্ট')
                                    ->url(route('fee.receipt', $payment))
                                    ->openUrlInNewTab(),
                            ])
                            ->send();
                    })
                    ->visible(fn(StudentFee $record): bool => $record->status !== 'paid' && $record->status !== 'waived'),

                Tables\Actions\Action::make('waive')
                    ->label('মওকুফ')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('এই ফি মওকুফ করতে চান?')
                    ->action(function (StudentFee $record): void {
                        $record->update([
                            'status' => 'waived',
                        ]);

                        Notification::make()
                            ->success()
                            ->title('ফি মওকুফ হয়েছে')
                            ->send();
                    })
                    ->visible(fn(StudentFee $record): bool => $record->status !== 'paid' && $record->status !== 'waived'),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('কোন ফি এসাইন করা হয়নি')
            ->emptyStateIcon('heroicon-o-credit-card');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeCollections::route('/'),
            'create' => Pages\CreateFeeCollection::route('/create'),
            'assign' => Pages\AssignFees::route('/assign'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::whereIn('status', ['pending', 'partial'])->count();
        return $pending ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}

// Helper function for Bengali months
if (!function_exists('bengaliMonth')) {
    function bengaliMonth($month): string
    {
        $months = [
            1 => 'জানুয়ারি',
            2 => 'ফেব্রুয়ারি',
            3 => 'মার্চ',
            4 => 'এপ্রিল',
            5 => 'মে',
            6 => 'জুন',
            7 => 'জুলাই',
            8 => 'আগস্ট',
            9 => 'সেপ্টেম্বর',
            10 => 'অক্টোবর',
            11 => 'নভেম্বর',
            12 => 'ডিসেম্বর'
        ];
        return $months[$month] ?? $month;
    }
}
