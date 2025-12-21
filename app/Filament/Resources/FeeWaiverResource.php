<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeWaiverResource\Pages;
use App\Models\FeeWaiver;
use App\Models\Student;
use App\Models\StudentFee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FeeWaiverResource extends BaseResource
{
    protected static ?string $model = FeeWaiver::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'ফি মওকুফ';

    protected static ?string $modelLabel = 'ফি মওকুফ';

    protected static ?string $pluralModelLabel = 'ফি মওকুফসমূহ';

    protected static ?string $navigationGroup = 'ফি ব্যবস্থাপনা';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('মওকুফের তথ্য')
                    ->description('কোন ছাত্রের কোন ফি মওকুফ করা হবে তা নির্বাচন করুন')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('ছাত্র')
                            ->searchable()
                            ->preload()
                            ->options(fn() => Student::active()->pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Forms\Set $set) => $set('student_fee_id', null)),

                        Forms\Components\Select::make('student_fee_id')
                            ->label('ফি')
                            ->options(function (Forms\Get $get) {
                                $studentId = $get('student_id');
                                if (!$studentId) {
                                    return [];
                                }
                                return StudentFee::where('student_id', $studentId)
                                    ->where('status', '!=', 'paid')
                                    ->with('feeStructure.feeType')
                                    ->get()
                                    ->mapWithKeys(function ($fee) {
                                        $name = $fee->feeStructure?->feeType?->name ?? 'ফি';
                                        $month = $fee->month ? " ({$fee->month}/{$fee->year})" : " ({$fee->year})";
                                        $due = number_format($fee->due_amount, 2);
                                        return [$fee->id => "{$name}{$month} - বকেয়া: ৳{$due}"];
                                    });
                            })
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\TextInput::make('waiver_amount')
                            ->label('মওকুফের পরিমাণ (৳)')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->prefix('৳')
                            ->helperText(function (Forms\Get $get) {
                                $feeId = $get('student_fee_id');
                                if ($feeId) {
                                    $fee = StudentFee::find($feeId);
                                    if ($fee) {
                                        return "সর্বোচ্চ: ৳" . number_format($fee->due_amount, 2);
                                    }
                                }
                                return null;
                            }),

                        Forms\Components\Textarea::make('reason')
                            ->label('মওকুফের কারণ')
                            ->placeholder('কেন এই ফি মওকুফ করা হচ্ছে তার বিস্তারিত কারণ লিখুন...')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
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
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('student.admission_no')
                    ->label('ভর্তি নং')
                    ->searchable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('studentFee.feeStructure.feeType.name')
                    ->label('ফি এর ধরণ')
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('waiver_amount')
                    ->label('মওকুফের পরিমাণ')
                    ->money('BDT')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('reason')
                    ->label('কারণ')
                    ->limit(30)
                    ->tooltip(fn(FeeWaiver $record) => $record->reason),

                Tables\Columns\IconColumn::make('is_approved')
                    ->label('অনুমোদিত')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\TextColumn::make('approvedBy.name')
                    ->label('অনুমোদনকারী')
                    ->placeholder('অপেক্ষমাণ'),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('অনুমোদনের তারিখ')
                    ->dateTime('d M Y, h:i A')
                    ->placeholder('অপেক্ষমাণ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('আবেদনের তারিখ')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('অনুমোদন স্ট্যাটাস')
                    ->placeholder('সব')
                    ->trueLabel('অনুমোদিত')
                    ->falseLabel('অপেক্ষমাণ')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('approved_at'),
                        false: fn(Builder $query) => $query->whereNull('approved_at'),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('অনুমোদন')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(FeeWaiver $record) => !$record->is_approved)
                    ->requiresConfirmation()
                    ->modalHeading('ফি মওকুফ অনুমোদন')
                    ->modalDescription('আপনি কি নিশ্চিত যে এই মওকুফ অনুমোদন করতে চান?')
                    ->action(function (FeeWaiver $record) {
                        $record->approve();
                        Notification::make()
                            ->success()
                            ->title('মওকুফ অনুমোদিত!')
                            ->body('ফি মওকুফ সফলভাবে অনুমোদন করা হয়েছে।')
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('প্রত্যাখ্যান')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(FeeWaiver $record) => !$record->is_approved)
                    ->requiresConfirmation()
                    ->modalHeading('ফি মওকুফ প্রত্যাখ্যান')
                    ->modalDescription('আপনি কি নিশ্চিত যে এই মওকুফ প্রত্যাখ্যান করতে চান? এটি মুছে ফেলা হবে।')
                    ->action(function (FeeWaiver $record) {
                        $record->reject();
                        Notification::make()
                            ->warning()
                            ->title('মওকুফ প্রত্যাখ্যাত!')
                            ->body('ফি মওকুফ প্রত্যাখ্যান করা হয়েছে।')
                            ->send();
                    }),

                Tables\Actions\ViewAction::make()
                    ->iconButton(),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->visible(fn(FeeWaiver $record) => !$record->is_approved),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('কোন ফি মওকুফ আবেদন নেই')
            ->emptyStateDescription('নতুন মওকুফ আবেদন করতে উপরের বাটনে ক্লিক করুন')
            ->emptyStateIcon('heroicon-o-document-check');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeWaivers::route('/'),
            'create' => Pages\CreateFeeWaiver::route('/create'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::pending()->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
