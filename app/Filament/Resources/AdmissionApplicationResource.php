<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdmissionApplicationResource\Pages;
use App\Models\AdmissionApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class AdmissionApplicationResource extends Resource
{
    protected static ?string $model = AdmissionApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';

    protected static ?string $navigationGroup = 'ভর্তি';

    protected static ?string $modelLabel = 'ভর্তি আবেদন';

    protected static ?string $pluralModelLabel = 'ভর্তি আবেদনসমূহ';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('আবেদন তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('application_no')
                                    ->label('আবেদন নং')
                                    ->default(fn() => AdmissionApplication::generateApplicationNo())
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\Select::make('academic_year_id')
                                    ->label('শিক্ষাবর্ষ')
                                    ->relationship('academicYear', 'name')
                                    ->required()
                                    ->native(false)
                                    ->default(fn() => \App\Models\AcademicYear::where('is_current', true)->first()?->id),

                                Forms\Components\Select::make('class_id')
                                    ->label('শ্রেণি')
                                    ->relationship('class', 'name')
                                    ->required()
                                    ->native(false),
                            ]),
                    ]),

                Forms\Components\Section::make('ছাত্র তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('student_name')
                                    ->label('নাম (বাংলা)')
                                    ->required(),

                                Forms\Components\TextInput::make('student_name_en')
                                    ->label('নাম (ইংরেজি)'),
                            ]),

                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->label('জন্ম তারিখ')
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('gender')
                                    ->label('লিঙ্গ')
                                    ->options(AdmissionApplication::genderOptions())
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('blood_group')
                                    ->label('রক্তের গ্রুপ'),

                                Forms\Components\TextInput::make('birth_certificate_no')
                                    ->label('জন্মনিবন্ধন নং'),
                            ]),
                    ]),

                Forms\Components\Section::make('অভিভাবক তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('father_name')
                                    ->label('পিতার নাম')
                                    ->required(),

                                Forms\Components\TextInput::make('father_phone')
                                    ->label('পিতার মোবাইল')
                                    ->tel()
                                    ->required(),

                                Forms\Components\TextInput::make('father_occupation')
                                    ->label('পিতার পেশা'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('mother_name')
                                    ->label('মাতার নাম')
                                    ->required(),

                                Forms\Components\TextInput::make('mother_phone')
                                    ->label('মাতার মোবাইল')
                                    ->tel(),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('ঠিকানা')
                    ->schema([
                        Forms\Components\Textarea::make('present_address')
                            ->label('বর্তমান ঠিকানা')
                            ->required()
                            ->rows(2),

                        Forms\Components\Textarea::make('permanent_address')
                            ->label('স্থায়ী ঠিকানা')
                            ->rows(2),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('স্ট্যাটাস')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('অবস্থা')
                            ->options(AdmissionApplication::statusOptions())
                            ->default('pending')
                            ->native(false)
                            ->disabled(fn($record) => $record === null),

                        Forms\Components\Textarea::make('remarks')
                            ->label('মন্তব্য')
                            ->rows(2),
                    ])
                    ->visible(fn($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('application_no')
                    ->label('আবেদন নং')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('student_name')
                    ->label('ছাত্রের নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('class.name')
                    ->label('শ্রেণি')
                    ->badge(),

                Tables\Columns\TextColumn::make('father_phone')
                    ->label('মোবাইল'),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => AdmissionApplication::statusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'admitted' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('আবেদনের তারিখ')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_id')
                    ->label('শ্রেণি')
                    ->relationship('class', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(AdmissionApplication::statusOptions()),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('অনুমোদন')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (AdmissionApplication $record): void {
                        $record->approve(Auth::id());
                        Notification::make()->success()->title('আবেদন অনুমোদিত!')->send();
                    })
                    ->visible(fn(AdmissionApplication $record): bool => $record->status === 'pending'),

                Tables\Actions\Action::make('reject')
                    ->label('প্রত্যাখ্যান')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('remarks')
                            ->label('কারণ')
                            ->required(),
                    ])
                    ->action(function (AdmissionApplication $record, array $data): void {
                        $record->reject(Auth::id(), $data['remarks']);
                        Notification::make()->warning()->title('আবেদন প্রত্যাখ্যাত!')->send();
                    })
                    ->visible(fn(AdmissionApplication $record): bool => $record->status === 'pending'),

                Tables\Actions\Action::make('print')
                    ->label('প্রিন্ট')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->action(function (AdmissionApplication $record) {
                        $data = [
                            'application' => $record,
                            'institute' => [
                                'name' => institution_name(),
                                'address' => institution_address(),
                                'phone' => institution_phone(),
                                'email' => institution_email(),
                            ],
                            'generated_at' => now()->format('d M Y, h:i A'),
                        ];

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.admission-form', $data)
                            ->setPaper('a4', 'portrait');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'admission-form-' . $record->application_no . '.pdf');
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('কোন আবেদন নেই')
            ->emptyStateIcon('heroicon-o-document-plus');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmissionApplications::route('/'),
            'create' => Pages\CreateAdmissionApplication::route('/create'),
            'view' => Pages\ViewAdmissionApplication::route('/{record}'),
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
