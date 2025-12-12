<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeInstallmentResource\Pages;
use App\Models\FeeInstallment;
use App\Models\StudentFee;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class FeeInstallmentResource extends Resource
{
    protected static ?string $model = FeeInstallment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'ফি ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'কিস্তি ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'কিস্তি';

    protected static ?string $pluralModelLabel = 'কিস্তিসমূহ';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('কিস্তির তথ্য')
                    ->description('কিস্তি পরিশোধের বিবরণ')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Forms\Components\Select::make('student_fee_id')
                            ->label('ছাত্র ফি')
                            ->relationship('studentFee', 'id')
                            ->getOptionLabelFromRecordUsing(function (StudentFee $record) {
                                return $record->student->name . ' - ৳' . number_format($record->final_amount, 2);
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn($record) => $record !== null),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('installment_no')
                                    ->label('কিস্তি নং')
                                    ->numeric()
                                    ->required()
                                    ->disabled(fn($record) => $record !== null),

                                Forms\Components\TextInput::make('amount')
                                    ->label('কিস্তির পরিমাণ')
                                    ->prefix('৳')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\DatePicker::make('due_date')
                                    ->label('পরিশোধের শেষ তারিখ')
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('paid_amount')
                                    ->label('পরিশোধিত')
                                    ->prefix('৳')
                                    ->numeric()
                                    ->default(0),

                                Forms\Components\DatePicker::make('paid_date')
                                    ->label('পরিশোধের তারিখ')
                                    ->native(false),

                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(FeeInstallment::getStatusOptions())
                                    ->default('pending')
                                    ->native(false),
                            ]),

                        Forms\Components\Textarea::make('remarks')
                            ->label('মন্তব্য')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('studentFee.student.student_id')
                    ->label('আইডি')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('studentFee.student.name')
                    ->label('ছাত্রের নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('studentFee.student.class.name')
                    ->label('শ্রেণি')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('installment_no')
                    ->label('কিস্তি নং')
                    ->formatStateUsing(fn($state) => "কিস্তি #{$state}")
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('পরিমাণ')
                    ->money('BDT')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('পরিশোধ')
                    ->money('BDT')
                    ->alignEnd()
                    ->color('success'),

                Tables\Columns\TextColumn::make('due_amount')
                    ->label('বাকি')
                    ->getStateUsing(fn($record) => max(0, $record->amount - $record->paid_amount))
                    ->money('BDT')
                    ->alignEnd()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('শেষ তারিখ')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn($record) => $record->due_date < now() && $record->status !== 'paid' ? 'danger' : null),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => FeeInstallment::getStatusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'partial' => 'info',
                        'overdue' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(FeeInstallment::getStatusOptions()),

                Tables\Filters\Filter::make('overdue')
                    ->label('মেয়াদোত্তীর্ণ')
                    ->query(fn(Builder $query) => $query->where('due_date', '<', now())->where('status', '!=', 'paid')),
            ])
            ->actions([
                Tables\Actions\Action::make('collect')
                    ->label('আদায়')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn($record) => $record->status !== 'paid')
                    ->form([
                        Forms\Components\TextInput::make('pay_amount')
                            ->label('পরিশোধের পরিমাণ')
                            ->prefix('৳')
                            ->numeric()
                            ->required()
                            ->default(fn($record) => $record->amount - $record->paid_amount),

                        Forms\Components\DatePicker::make('pay_date')
                            ->label('তারিখ')
                            ->default(now())
                            ->native(false),

                        Forms\Components\Textarea::make('remarks')
                            ->label('মন্তব্য')
                            ->rows(2),
                    ])
                    ->action(function (FeeInstallment $record, array $data) {
                        $record->update([
                            'paid_amount' => $record->paid_amount + $data['pay_amount'],
                            'paid_date' => $data['pay_date'],
                            'status' => ($record->paid_amount + $data['pay_amount']) >= $record->amount ? 'paid' : 'partial',
                            'collected_by' => auth()->id(),
                            'remarks' => $data['remarks'],
                        ]);

                        // Update parent StudentFee
                        $studentFee = $record->studentFee;
                        $totalPaid = $studentFee->installments()->sum('paid_amount');
                        $studentFee->update([
                            'paid_amount' => $totalPaid,
                            'due_amount' => $studentFee->final_amount - $totalPaid,
                            'status' => $totalPaid >= $studentFee->final_amount ? 'paid' : ($totalPaid > 0 ? 'partial' : 'pending'),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('কিস্তি আদায় সম্পন্ন!')
                            ->body('৳' . number_format($data['pay_amount'], 2) . ' আদায় হয়েছে।')
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('due_date', 'asc')
            ->striped()
            ->emptyStateHeading('কোন কিস্তি নেই')
            ->emptyStateDescription('প্রথমে ছাত্রের ফি এসাইন করুন এবং কিস্তিতে ভাগ করুন')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeInstallments::route('/'),
            'create' => Pages\CreateFeeInstallment::route('/create'),
            'edit' => Pages\EditFeeInstallment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
