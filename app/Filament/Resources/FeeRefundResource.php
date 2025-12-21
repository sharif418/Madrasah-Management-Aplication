<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeRefundResource\Pages;
use App\Models\FeeRefund;
use App\Models\Student;
use App\Models\FeePayment;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class FeeRefundResource extends BaseResource
{
    protected static ?string $model = FeeRefund::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static ?string $navigationGroup = 'ফি ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'ফি ফেরত';

    protected static ?string $modelLabel = 'ফি ফেরত';

    protected static ?string $pluralModelLabel = 'ফি ফেরতসমূহ';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ফেরতের তথ্য')
                    ->description('ফি ফেরতের আবেদন')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('student_id')
                                    ->label('ছাত্র নির্বাচন')
                                    ->relationship('student', 'name')
                                    ->getOptionLabelFromRecordUsing(fn(Student $record) => "{$record->student_id} - {$record->name}")
                                    ->searchable(['name', 'student_id'])
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn(Forms\Set $set) => $set('fee_payment_id', null)),

                                Forms\Components\Select::make('fee_payment_id')
                                    ->label('পেমেন্ট নির্বাচন (ঐচ্ছিক)')
                                    ->options(function (Forms\Get $get) {
                                        $studentId = $get('student_id');
                                        if (!$studentId)
                                            return [];

                                        return FeePayment::where('student_id', $studentId)
                                            ->with('studentFee.feeStructure.feeType')
                                            ->get()
                                            ->mapWithKeys(fn($p) => [
                                                $p->id => "#{$p->receipt_no} - ৳" . number_format($p->amount, 2)
                                            ]);
                                    })
                                    ->helperText('যে পেমেন্ট ফেরত দেওয়া হবে')
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('refund_amount')
                                    ->label('ফেরতের পরিমাণ')
                                    ->prefix('৳')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1),

                                Forms\Components\Select::make('refund_method')
                                    ->label('ফেরতের মাধ্যম')
                                    ->options(FeeRefund::getMethodOptions())
                                    ->default('cash')
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(FeeRefund::getStatusOptions())
                                    ->default('pending')
                                    ->required()
                                    ->native(false)
                                    ->disabled(fn($record) => $record === null),
                            ]),

                        Forms\Components\Textarea::make('reason')
                            ->label('ফেরতের কারণ')
                            ->required()
                            ->rows(3)
                            ->placeholder('বিস্তারিত কারণ লিখুন...')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('transaction_id')
                                    ->label('ট্রানজেকশন আইডি')
                                    ->placeholder('বিকাশ/নগদ/ব্যাংক রেফারেন্স'),

                                Forms\Components\DatePicker::make('refund_date')
                                    ->label('ফেরতের তারিখ')
                                    ->native(false),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('refund_no')
                    ->label('রেফ নং')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('student.student_id')
                    ->label('ছাত্র আইডি')
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('ছাত্রের নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('student.class.name')
                    ->label('শ্রেণি')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('refund_amount')
                    ->label('পরিমাণ')
                    ->money('BDT')
                    ->alignEnd()
                    ->weight('bold')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('refund_method')
                    ->label('মাধ্যম')
                    ->formatStateUsing(fn($state) => FeeRefund::getMethodOptions()[$state] ?? $state)
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => FeeRefund::getStatusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(FeeRefund::getStatusOptions()),

                Tables\Filters\SelectFilter::make('refund_method')
                    ->label('মাধ্যম')
                    ->options(FeeRefund::getMethodOptions()),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('অনুমোদন')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('ফি ফেরত অনুমোদন')
                    ->modalDescription('আপনি কি এই ফি ফেরত অনুমোদন করতে চান?')
                    ->action(function (FeeRefund $record) {
                        $record->approve(auth()->id());

                        Notification::make()
                            ->success()
                            ->title('অনুমোদিত!')
                            ->body('ফি ফেরত অনুমোদন করা হয়েছে।')
                            ->send();
                    }),

                Tables\Actions\Action::make('complete')
                    ->label('সম্পন্ন')
                    ->icon('heroicon-o-banknotes')
                    ->color('info')
                    ->visible(fn($record) => $record->status === 'approved')
                    ->form([
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('ট্রানজেকশন আইডি'),
                    ])
                    ->action(function (FeeRefund $record, array $data) {
                        $record->complete($data['transaction_id'] ?? null);

                        Notification::make()
                            ->success()
                            ->title('সম্পন্ন!')
                            ->body('ফি ফেরত দেওয়া হয়েছে।')
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('বাতিল')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn($record) => in_array($record->status, ['pending', 'approved']))
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('বাতিলের কারণ')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (FeeRefund $record, array $data) {
                        $record->reject($data['rejection_reason'], auth()->id());

                        Notification::make()
                            ->warning()
                            ->title('বাতিল!')
                            ->body('ফি ফেরত বাতিল করা হয়েছে।')
                            ->send();
                    }),

                Tables\Actions\ViewAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('কোন ফেরত আবেদন নেই')
            ->emptyStateDescription('ফি ফেরত আবেদন করতে নতুন বাটনে ক্লিক করুন')
            ->emptyStateIcon('heroicon-o-arrow-uturn-left');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeRefunds::route('/'),
            'create' => Pages\CreateFeeRefund::route('/create'),
            'edit' => Pages\EditFeeRefund::route('/{record}/edit'),
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
