<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaryPaymentResource\Pages;
use App\Models\SalaryPayment;
use App\Models\Teacher;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class SalaryPaymentResource extends BaseResource
{
    protected static ?string $model = SalaryPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'বেতন';

    protected static ?string $pluralModelLabel = 'বেতন পরিশোধ';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('কর্মচারী তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('employee_type')
                                    ->label('কর্মচারীর ধরণ')
                                    ->options([
                                        'teacher' => 'শিক্ষক',
                                        'staff' => 'স্টাফ',
                                    ])
                                    ->required()
                                    ->live()
                                    ->native(false)
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('employee_id', null);
                                        $set('basic_salary', 0);
                                    }),

                                Forms\Components\Select::make('employee_id')
                                    ->label('নাম')
                                    ->options(function (Get $get) {
                                        if ($get('employee_type') === 'teacher') {
                                            return Teacher::where('status', 'active')
                                                ->pluck('name', 'id');
                                        }
                                        return Staff::where('status', 'active')
                                            ->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->native(false)
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        if ($get('employee_type') === 'teacher' && $state) {
                                            $teacher = Teacher::find($state);
                                            $set('basic_salary', $teacher?->basic_salary ?? 0);
                                        } elseif ($state) {
                                            $staff = Staff::find($state);
                                            $set('basic_salary', $staff?->basic_salary ?? 0);
                                        }
                                    }),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('month')
                                    ->label('মাস')
                                    ->options(SalaryPayment::monthOptions())
                                    ->default(now()->month)
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('year')
                                    ->label('সাল')
                                    ->numeric()
                                    ->default(now()->year)
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Section::make('বেতন হিসাব')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('basic_salary')
                                    ->label('মূল বেতন')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::updateNetSalary($set, $get)),

                                Forms\Components\TextInput::make('allowances')
                                    ->label('ভাতা')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::updateNetSalary($set, $get)),

                                Forms\Components\TextInput::make('bonus')
                                    ->label('বোনাস')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::updateNetSalary($set, $get)),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('deductions')
                                    ->label('কর্তন')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::updateNetSalary($set, $get)),

                                Forms\Components\TextInput::make('advance_deduction')
                                    ->label('অগ্রিম কর্তন')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn(Set $set, Get $get) => self::updateNetSalary($set, $get)),

                                Forms\Components\TextInput::make('net_salary')
                                    ->label('নিট বেতন')
                                    ->numeric()
                                    ->prefix('৳')
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                    ]),

                Forms\Components\Section::make('পেমেন্ট')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(SalaryPayment::statusOptions())
                                    ->default('pending')
                                    ->native(false),

                                Forms\Components\DatePicker::make('payment_date')
                                    ->label('পেমেন্টের তারিখ')
                                    ->native(false),

                                Forms\Components\Select::make('payment_method')
                                    ->label('পদ্ধতি')
                                    ->options(SalaryPayment::paymentMethodOptions())
                                    ->native(false),
                            ]),

                        Forms\Components\Textarea::make('remarks')
                            ->label('মন্তব্য')
                            ->rows(2),

                        Forms\Components\Hidden::make('paid_by')
                            ->default(fn() => Auth::id()),
                    ]),
            ]);
    }

    protected static function updateNetSalary(Set $set, Get $get): void
    {
        $basic = (float) ($get('basic_salary') ?? 0);
        $allowances = (float) ($get('allowances') ?? 0);
        $bonus = (float) ($get('bonus') ?? 0);
        $deductions = (float) ($get('deductions') ?? 0);
        $advance = (float) ($get('advance_deduction') ?? 0);

        $net = $basic + $allowances + $bonus - $deductions - $advance;
        $set('net_salary', $net);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_type')
                    ->label('ধরণ')
                    ->formatStateUsing(fn($state) => $state === 'teacher' ? 'শিক্ষক' : 'স্টাফ')
                    ->badge()
                    ->color(fn($state) => $state === 'teacher' ? 'info' : 'warning'),

                Tables\Columns\TextColumn::make('employee.name')
                    ->label('নাম')
                    ->weight('bold')
                    ->getStateUsing(function ($record) {
                        return $record->employee?->name ?? 'N/A';
                    }),

                Tables\Columns\TextColumn::make('month')
                    ->label('মাস')
                    ->formatStateUsing(fn($state) => SalaryPayment::monthOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('year')
                    ->label('সাল'),

                Tables\Columns\TextColumn::make('basic_salary')
                    ->label('মূল বেতন')
                    ->money('BDT'),

                Tables\Columns\TextColumn::make('net_salary')
                    ->label('নিট বেতন')
                    ->money('BDT')
                    ->weight('bold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => SalaryPayment::statusOptions()[$state] ?? $state)
                    ->color(fn($state) => $state === 'paid' ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('পেমেন্ট')
                    ->date('d M Y')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee_type')
                    ->label('ধরণ')
                    ->options([
                        'teacher' => 'শিক্ষক',
                        'staff' => 'স্টাফ',
                    ]),

                Tables\Filters\SelectFilter::make('month')
                    ->label('মাস')
                    ->options(SalaryPayment::monthOptions()),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(SalaryPayment::statusOptions()),
            ])
            ->actions([
                Tables\Actions\Action::make('pay')
                    ->label('পরিশোধ')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (SalaryPayment $record): void {
                        $record->status = 'paid';
                        $record->payment_date = now();
                        $record->paid_by = Auth::id();
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('বেতন পরিশোধ হয়েছে!')
                            ->send();
                    })
                    ->visible(fn(SalaryPayment $record): bool => $record->status === 'pending'),

                Tables\Actions\Action::make('payslip')
                    ->label('পে-স্লিপ')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function (SalaryPayment $record) {
                        $data = [
                            'payment' => $record,
                            'employee' => $record->employee,
                            'institute' => [
                                'name' => institution_name(),
                                'address' => institution_address(),
                                'phone' => institution_phone(),
                                'email' => institution_email(),
                            ],
                            'generated_at' => now()->format('d M Y, h:i A'),
                        ];

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.payslip', $data)
                            ->setPaper('a5', 'portrait');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'payslip-' . $record->id . '-' . now()->timestamp . '.pdf');
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('কোন বেতন রেকর্ড নেই')
            ->emptyStateIcon('heroicon-o-banknotes');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalaryPayments::route('/'),
            'create' => Pages\CreateSalaryPayment::route('/create'),
            'edit' => Pages\EditSalaryPayment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
