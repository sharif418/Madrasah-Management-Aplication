<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use App\Models\ClassName;
use App\Models\Section;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ActionGroup;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Collection;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;

class StudentResource extends BaseResource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'ছাত্র ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'ছাত্র';

    protected static ?string $pluralModelLabel = 'ছাত্রগণ';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Header with Photo
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('photo')
                                    ->label('ছবি')
                                    ->collection('photo')
                                    ->image()
                                    ->imageEditor()
                                    ->avatar()
                                    ->circleCropper()
                                    ->columnSpan(1),

                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('admission_info')
                                            ->label('')
                                            ->content(
                                                fn(?Student $record): string =>
                                                $record ? "ভর্তি নং: {$record->admission_no}" : 'নতুন ছাত্র'
                                            ),
                                    ])
                                    ->columnSpan(3)
                                    ->visible(fn(?Student $record) => $record !== null),
                            ]),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Tabs::make('ছাত্রের তথ্য')
                    ->tabs([
                        // Tab 1: Basic Information
                        Forms\Components\Tabs\Tab::make('মৌলিক তথ্য')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('admission_no')
                                            ->label('ভর্তি নম্বর')
                                            ->default(fn() => Student::generateAdmissionNo())
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(20)
                                            ->disabled(fn(?Student $record) => $record !== null)
                                            ->dehydrated(),

                                        Forms\Components\TextInput::make('roll_no')
                                            ->label('রোল নম্বর')
                                            ->maxLength(20),

                                        Forms\Components\DatePicker::make('admission_date')
                                            ->label('ভর্তির তারিখ')
                                            ->default(now())
                                            ->required()
                                            ->native(false),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('নাম (বাংলায়)')
                                            ->placeholder('সম্পূর্ণ নাম বাংলায় লিখুন')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('name_en')
                                            ->label('নাম (ইংরেজিতে)')
                                            ->placeholder('Full name in English')
                                            ->maxLength(255),
                                    ]),

                                Forms\Components\Fieldset::make('পিতার তথ্য')
                                    ->schema([
                                        Forms\Components\TextInput::make('father_name')
                                            ->label('পিতার নাম')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('father_phone')
                                            ->label('পিতার মোবাইল')
                                            ->tel()
                                            ->prefix('+880')
                                            ->maxLength(15),

                                        Forms\Components\TextInput::make('father_occupation')
                                            ->label('পিতার পেশা')
                                            ->maxLength(100),
                                    ])
                                    ->columns(3),

                                Forms\Components\Fieldset::make('মাতার তথ্য')
                                    ->schema([
                                        Forms\Components\TextInput::make('mother_name')
                                            ->label('মাতার নাম')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('mother_phone')
                                            ->label('মাতার মোবাইল')
                                            ->tel()
                                            ->prefix('+880')
                                            ->maxLength(15),
                                    ])
                                    ->columns(2),

                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\DatePicker::make('date_of_birth')
                                            ->label('জন্ম তারিখ')
                                            ->required()
                                            ->native(false)
                                            ->maxDate(now()->subYears(3)),

                                        Forms\Components\Select::make('gender')
                                            ->label('লিঙ্গ')
                                            ->options([
                                                'male' => 'ছেলে',
                                                'female' => 'মেয়ে',
                                            ])
                                            ->required()
                                            ->native(false),

                                        Forms\Components\Select::make('blood_group')
                                            ->label('রক্তের গ্রুপ')
                                            ->options([
                                                'A+' => 'A+',
                                                'A-' => 'A-',
                                                'B+' => 'B+',
                                                'B-' => 'B-',
                                                'AB+' => 'AB+',
                                                'AB-' => 'AB-',
                                                'O+' => 'O+',
                                                'O-' => 'O-',
                                            ])
                                            ->native(false)
                                            ->searchable(),

                                        Forms\Components\TextInput::make('birth_certificate_no')
                                            ->label('জন্ম সনদ নম্বর')
                                            ->maxLength(50),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('religion')
                                            ->label('ধর্ম')
                                            ->options([
                                                'ইসলাম' => 'ইসলাম',
                                                'হিন্দু' => 'হিন্দু',
                                                'বৌদ্ধ' => 'বৌদ্ধ',
                                                'খ্রিস্টান' => 'খ্রিস্টান',
                                                'অন্যান্য' => 'অন্যান্য',
                                            ])
                                            ->default('ইসলাম')
                                            ->native(false),

                                        Forms\Components\TextInput::make('nationality')
                                            ->label('জাতীয়তা')
                                            ->default('বাংলাদেশী')
                                            ->maxLength(50),
                                    ]),
                            ]),

                        // Tab 2: Academic Information
                        Forms\Components\Tabs\Tab::make('একাডেমিক তথ্য')
                            ->icon('heroicon-o-book-open')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('academic_year_id')
                                            ->label('শিক্ষাবর্ষ')
                                            ->relationship('academicYear', 'name')
                                            ->default(fn() => AcademicYear::current()?->id)
                                            ->required()
                                            ->native(false)
                                            ->preload(),

                                        Forms\Components\Select::make('class_id')
                                            ->label('শ্রেণি')
                                            ->relationship('class', 'name', fn(Builder $query) => $query->where('is_active', true)->orderBy('order'))
                                            ->required()
                                            ->native(false)
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(fn(Forms\Set $set) => $set('section_id', null)),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('section_id')
                                            ->label('শাখা')
                                            ->options(function (Forms\Get $get) {
                                                $classId = $get('class_id');
                                                if (!$classId)
                                                    return [];
                                                return Section::where('class_id', $classId)
                                                    ->where('is_active', true)
                                                    ->pluck('name', 'id');
                                            })
                                            ->native(false)
                                            ->preload(),

                                        Forms\Components\Select::make('shift_id')
                                            ->label('শিফট')
                                            ->relationship('shift', 'name', fn(Builder $query) => $query->where('is_active', true))
                                            ->native(false)
                                            ->preload(),
                                    ]),

                                Forms\Components\Fieldset::make('পূর্ববর্তী শিক্ষা')
                                    ->schema([
                                        Forms\Components\TextInput::make('previous_school')
                                            ->label('পূর্ববর্তী প্রতিষ্ঠান')
                                            ->placeholder('যেখান থেকে এসেছে')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('previous_class')
                                            ->label('পূর্ববর্তী শ্রেণি')
                                            ->placeholder('কোন শ্রেণি পর্যন্ত পড়েছে')
                                            ->maxLength(100),
                                    ])
                                    ->columns(2),

                                Forms\Components\Toggle::make('is_boarder')
                                    ->label('আবাসিক ছাত্র')
                                    ->helperText('হোস্টেলে থাকলে চালু করুন')
                                    ->inline(false),
                            ]),

                        // Tab 3: Contact Information
                        Forms\Components\Tabs\Tab::make('যোগাযোগ')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('phone')
                                            ->label('ছাত্রের মোবাইল')
                                            ->tel()
                                            ->prefix('+880')
                                            ->maxLength(15),

                                        Forms\Components\TextInput::make('email')
                                            ->label('ইমেইল')
                                            ->email()
                                            ->maxLength(255),
                                    ]),

                                Forms\Components\Textarea::make('present_address')
                                    ->label('বর্তমান ঠিকানা')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('permanent_address')
                                    ->label('স্থায়ী ঠিকানা')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('guardian_id')
                                    ->label('অভিভাবক')
                                    ->relationship('guardian', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('অভিভাবকের নাম')
                                            ->required(),
                                        Forms\Components\Select::make('relation')
                                            ->label('সম্পর্ক')
                                            ->options([
                                                'পিতা' => 'পিতা',
                                                'মাতা' => 'মাতা',
                                                'ভাই' => 'ভাই',
                                                'চাচা' => 'চাচা',
                                                'মামা' => 'মামা',
                                                'অন্যান্য' => 'অন্যান্য',
                                            ])
                                            ->required(),
                                        Forms\Components\TextInput::make('phone')
                                            ->label('মোবাইল')
                                            ->tel()
                                            ->required(),
                                        Forms\Components\TextInput::make('occupation')
                                            ->label('পেশা'),
                                        Forms\Components\Textarea::make('address')
                                            ->label('ঠিকানা'),
                                    ])
                                    ->createOptionModalHeading('নতুন অভিভাবক যোগ করুন'),
                            ]),

                        // Tab 4: Additional Information
                        Forms\Components\Tabs\Tab::make('অন্যান্য')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options([
                                        'active' => 'সক্রিয়',
                                        'inactive' => 'নিষ্ক্রিয়',
                                        'transferred' => 'বদলি',
                                        'dropped_out' => 'ঝরে পড়া',
                                        'passed_out' => 'পাস করেছে',
                                        'suspended' => 'সাময়িক বহিষ্কার',
                                    ])
                                    ->default('active')
                                    ->required()
                                    ->native(false),

                                Forms\Components\Textarea::make('medical_conditions')
                                    ->label('স্বাস্থ্য সংক্রান্ত তথ্য')
                                    ->placeholder('বিশেষ কোন রোগ বা এলার্জি থাকলে লিখুন')
                                    ->rows(2),

                                Forms\Components\Textarea::make('notes')
                                    ->label('মন্তব্য/নোট')
                                    ->placeholder('অতিরিক্ত কোন তথ্য...')
                                    ->rows(2),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')
                    ->label('')
                    ->collection('photo')
                    ->circular()
                    ->defaultImageUrl(fn() => asset('images/default-student.png'))
                    ->size(40),

                Tables\Columns\TextColumn::make('admission_no')
                    ->label('ভর্তি নং')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('name')
                    ->label('নাম')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn(Student $record): string => $record->father_name),

                Tables\Columns\TextColumn::make('class.name')
                    ->label('শ্রেণি')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('section.name')
                    ->label('শাখা')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('roll_no')
                    ->label('রোল')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('father_phone')
                    ->label('অভিভাবক মোবাইল')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('gender')
                    ->label('লিঙ্গ')
                    ->formatStateUsing(fn(string $state): string => $state === 'male' ? 'ছেলে' : 'মেয়ে')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'male' ? 'info' : 'pink')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_boarder')
                    ->label('আবাসিক')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'সক্রিয়',
                        'inactive' => 'নিষ্ক্রিয়',
                        'transferred' => 'বদলি',
                        'dropped_out' => 'ঝরে পড়া',
                        'passed_out' => 'পাস',
                        'suspended' => 'বহিষ্কার',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('admission_date')
                    ->label('ভর্তির তারিখ')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_id')
                    ->label('শ্রেণি')
                    ->relationship('class', 'name')
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('section_id')
                    ->label('শাখা')
                    ->relationship('section', 'name')
                    ->preload(),

                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('শিক্ষাবর্ষ')
                    ->relationship('academicYear', 'name')
                    ->preload()
                    ->default(fn() => AcademicYear::current()?->id),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options([
                        'active' => 'সক্রিয়',
                        'inactive' => 'নিষ্ক্রিয়',
                        'transferred' => 'বদলি',
                        'dropped_out' => 'ঝরে পড়া',
                        'passed_out' => 'পাস করেছে',
                        'suspended' => 'বহিষ্কার',
                    ]),

                Tables\Filters\TernaryFilter::make('is_boarder')
                    ->label('আবাসিক'),

                Tables\Filters\SelectFilter::make('gender')
                    ->label('লিঙ্গ')
                    ->options([
                        'male' => 'ছেলে',
                        'female' => 'মেয়ে',
                    ]),

                Tables\Filters\TrashedFilter::make()
                    ->label('মুছে ফেলা'),
            ])
            ->filtersFormColumns(3)
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('দেখুন'),
                    Tables\Actions\EditAction::make()
                        ->label('সম্পাদনা'),
                    Tables\Actions\Action::make('idCard')
                        ->label('আইডি কার্ড')
                        ->icon('heroicon-o-identification')
                        ->url(fn(Student $record): string => route('student.id-card', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('tc')
                        ->label('টিসি/ছাড়পত্র')
                        ->icon('heroicon-o-document-text')
                        ->url(fn(Student $record): string => route('student.tc', $record))
                        ->openUrlInNewTab()
                        ->visible(fn(Student $record): bool => $record->status !== 'active'),
                    Tables\Actions\Action::make('convertToAlumni')
                        ->label('প্রাক্তন ছাত্র করুন')
                        ->icon('heroicon-o-academic-cap')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('প্রাক্তন ছাত্র তালিকায় যোগ করুন')
                        ->modalDescription('এই ছাত্রকে প্রাক্তন ছাত্র হিসেবে রেকর্ড করতে চান?')
                        ->form([
                            Forms\Components\TextInput::make('passing_year')
                                ->label('পাস সাল')
                                ->numeric()
                                ->default(date('Y'))
                                ->required(),
                            Forms\Components\TextInput::make('current_occupation')
                                ->label('বর্তমান পেশা (ঐচ্ছিক)'),
                        ])
                        ->action(function (Student $record, array $data): void {
                            \App\Models\Alumni::create([
                                'student_id' => $record->id,
                                'name' => $record->name,
                                'phone' => $record->father_phone ?? $record->phone,
                                'email' => $record->email,
                                'passing_year' => $data['passing_year'],
                                'last_class' => $record->class?->name,
                                'current_occupation' => $data['current_occupation'] ?? null,
                            ]);

                            $record->update(['status' => 'passed_out']);

                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('সফল!')
                                ->body('ছাত্র প্রাক্তন তালিকায় যোগ হয়েছে।')
                                ->send();
                        })
                        ->visible(fn(Student $record): bool =>
                            $record->status === 'passed_out' || $record->status === 'active'),
                    Tables\Actions\DeleteAction::make()
                        ->label('মুছে ফেলুন')
                        ->requiresConfirmation(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\BulkAction::make('updateStatus')
                        ->label('স্ট্যাটাস পরিবর্তন')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('নতুন স্ট্যাটাস')
                                ->options([
                                    'active' => 'সক্রিয়',
                                    'inactive' => 'নিষ্ক্রিয়',
                                    'transferred' => 'বদলি',
                                    'dropped_out' => 'ঝরে পড়া',
                                    'passed_out' => 'পাস করেছে',
                                    'suspended' => 'বহিষ্কার',
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(fn(Student $record) => $record->update(['status' => $data['status']]));
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('updateSection')
                        ->label('শাখা পরিবর্তন')
                        ->icon('heroicon-o-arrows-right-left')
                        ->form([
                            Forms\Components\Select::make('section_id')
                                ->label('নতুন শাখা')
                                ->relationship('section', 'name')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(fn(Student $record) => $record->update(['section_id' => $data['section_id']]));
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('কোন ছাত্র নেই')
            ->emptyStateDescription('নতুন ছাত্র ভর্তি করতে নিচের বাটনে ক্লিক করুন')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                SpatieMediaLibraryImageEntry::make('photo')
                                    ->label('')
                                    ->collection('photo')
                                    ->circular()
                                    ->defaultImageUrl(fn() => asset('images/default-student.png'))
                                    ->size(100),

                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('name')
                                        ->label('নাম')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold),
                                    Infolists\Components\TextEntry::make('admission_no')
                                        ->label('ভর্তি নম্বর')
                                        ->badge()
                                        ->color('primary'),
                                    Infolists\Components\TextEntry::make('class.name')
                                        ->label('শ্রেণি'),
                                ])
                                    ->columnSpan(3),
                            ]),
                    ]),

                Infolists\Components\Tabs::make()
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('ব্যক্তিগত তথ্য')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Infolists\Components\Grid::make(3)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('father_name')
                                            ->label('পিতার নাম'),
                                        Infolists\Components\TextEntry::make('father_phone')
                                            ->label('পিতার মোবাইল')
                                            ->icon('heroicon-o-phone'),
                                        Infolists\Components\TextEntry::make('father_occupation')
                                            ->label('পিতার পেশা'),
                                        Infolists\Components\TextEntry::make('mother_name')
                                            ->label('মাতার নাম'),
                                        Infolists\Components\TextEntry::make('mother_phone')
                                            ->label('মাতার মোবাইল'),
                                        Infolists\Components\TextEntry::make('date_of_birth')
                                            ->label('জন্ম তারিখ')
                                            ->date('d M Y'),
                                        Infolists\Components\TextEntry::make('gender')
                                            ->label('লিঙ্গ')
                                            ->formatStateUsing(fn(string $state): string => $state === 'male' ? 'ছেলে' : 'মেয়ে'),
                                        Infolists\Components\TextEntry::make('blood_group')
                                            ->label('রক্তের গ্রুপ')
                                            ->badge(),
                                        Infolists\Components\TextEntry::make('religion')
                                            ->label('ধর্ম'),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make('একাডেমিক তথ্য')
                            ->icon('heroicon-o-book-open')
                            ->schema([
                                Infolists\Components\Grid::make(3)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('academicYear.name')
                                            ->label('শিক্ষাবর্ষ'),
                                        Infolists\Components\TextEntry::make('class.name')
                                            ->label('শ্রেণি')
                                            ->badge()
                                            ->color('info'),
                                        Infolists\Components\TextEntry::make('section.name')
                                            ->label('শাখা'),
                                        Infolists\Components\TextEntry::make('roll_no')
                                            ->label('রোল নম্বর'),
                                        Infolists\Components\TextEntry::make('admission_date')
                                            ->label('ভর্তির তারিখ')
                                            ->date('d M Y'),
                                        Infolists\Components\IconEntry::make('is_boarder')
                                            ->label('আবাসিক')
                                            ->boolean(),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make('যোগাযোগ')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('phone')
                                            ->label('মোবাইল'),
                                        Infolists\Components\TextEntry::make('email')
                                            ->label('ইমেইল'),
                                        Infolists\Components\TextEntry::make('present_address')
                                            ->label('বর্তমান ঠিকানা')
                                            ->columnSpanFull(),
                                        Infolists\Components\TextEntry::make('permanent_address')
                                            ->label('স্থায়ী ঠিকানা')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentsRelationManager::class,
            RelationManagers\EnrollmentsRelationManager::class,
            RelationManagers\AttendancesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'শ্রেণি' => $record->class?->name,
            'পিতা' => $record->father_name,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'name_en', 'admission_no', 'father_name', 'father_phone'];
    }
}
