<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Filament\Resources\TeacherResource\RelationManagers;
use App\Models\Teacher;
use App\Models\Department;
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

class TeacherResource extends BaseResource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'শিক্ষক ও স্টাফ';

    protected static ?string $modelLabel = 'শিক্ষক';

    protected static ?string $pluralModelLabel = 'শিক্ষকমণ্ডলী';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Header Section with Photo
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->label('ছবি')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('teachers/photos')
                                    ->avatar()
                                    ->circleCropper()
                                    ->columnSpan(1),

                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('employee_info')
                                            ->label('')
                                            ->content(
                                                fn(?Teacher $record): string =>
                                                $record ? "কর্মচারী আইডি: {$record->employee_id}" : 'নতুন শিক্ষক'
                                            ),
                                    ])
                                    ->columnSpan(3)
                                    ->visible(fn(?Teacher $record) => $record !== null),
                            ]),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Tabs::make('শিক্ষকের তথ্য')
                    ->tabs([
                        // Tab 1: Basic Information
                        Forms\Components\Tabs\Tab::make('ব্যক্তিগত তথ্য')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('employee_id')
                                            ->label('কর্মচারী আইডি')
                                            ->default(fn() => Teacher::generateEmployeeId())
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(20)
                                            ->disabled(fn(?Teacher $record) => $record !== null)
                                            ->dehydrated(),

                                        Forms\Components\TextInput::make('name')
                                            ->label('নাম (বাংলায়)')
                                            ->placeholder('সম্পূর্ণ নাম বাংলায়')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('name_en')
                                            ->label('নাম (ইংরেজিতে)')
                                            ->placeholder('Full name in English')
                                            ->maxLength(255),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('father_name')
                                            ->label('পিতার নাম')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('mother_name')
                                            ->label('মাতার নাম')
                                            ->maxLength(255),
                                    ]),

                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\DatePicker::make('date_of_birth')
                                            ->label('জন্ম তারিখ')
                                            ->required()
                                            ->native(false)
                                            ->maxDate(now()->subYears(18)),

                                        Forms\Components\Select::make('gender')
                                            ->label('লিঙ্গ')
                                            ->options([
                                                'male' => 'পুরুষ',
                                                'female' => 'মহিলা',
                                            ])
                                            ->required()
                                            ->native(false),

                                        Forms\Components\Select::make('marital_status')
                                            ->label('বৈবাহিক অবস্থা')
                                            ->options([
                                                'single' => 'অবিবাহিত',
                                                'married' => 'বিবাহিত',
                                                'divorced' => 'তালাকপ্রাপ্ত',
                                                'widowed' => 'বিধবা/বিপত্নীক',
                                            ])
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
                                            ->native(false),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('nid')
                                            ->label('জাতীয় পরিচয়পত্র নম্বর')
                                            ->maxLength(20),

                                        Forms\Components\TextInput::make('religion')
                                            ->label('ধর্ম')
                                            ->default('ইসলাম')
                                            ->maxLength(50),
                                    ]),
                            ]),

                        // Tab 2: Contact Information
                        Forms\Components\Tabs\Tab::make('যোগাযোগ')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('phone')
                                            ->label('মোবাইল নম্বর')
                                            ->tel()
                                            ->prefix('+880')
                                            ->required()
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

                                Forms\Components\Fieldset::make('জরুরি যোগাযোগ')
                                    ->schema([
                                        Forms\Components\TextInput::make('emergency_contact_name')
                                            ->label('নাম')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('emergency_contact_phone')
                                            ->label('মোবাইল')
                                            ->tel()
                                            ->maxLength(15),

                                        Forms\Components\TextInput::make('emergency_contact_relation')
                                            ->label('সম্পর্ক')
                                            ->maxLength(100),
                                    ])
                                    ->columns(3),
                            ]),

                        // Tab 3: Employment Information
                        Forms\Components\Tabs\Tab::make('চাকরি সংক্রান্ত')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('department_id')
                                            ->label('বিভাগ')
                                            ->relationship('department', 'name', fn(Builder $query) => $query->where('is_active', true))
                                            ->required()
                                            ->native(false)
                                            ->preload()
                                            ->searchable(),

                                        Forms\Components\Select::make('designation_id')
                                            ->label('পদবী')
                                            ->relationship('designation', 'name', fn(Builder $query) => $query->where('is_active', true)->orderBy('order'))
                                            ->required()
                                            ->native(false)
                                            ->preload()
                                            ->searchable(),
                                    ]),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\DatePicker::make('joining_date')
                                            ->label('যোগদানের তারিখ')
                                            ->required()
                                            ->native(false)
                                            ->maxDate(now()),

                                        Forms\Components\Select::make('employment_type')
                                            ->label('চাকরির ধরণ')
                                            ->options([
                                                'permanent' => 'স্থায়ী',
                                                'temporary' => 'অস্থায়ী',
                                                'contractual' => 'চুক্তিভিত্তিক',
                                                'part_time' => 'পার্ট-টাইম',
                                            ])
                                            ->default('permanent')
                                            ->required()
                                            ->native(false),

                                        Forms\Components\TextInput::make('basic_salary')
                                            ->label('মূল বেতন')
                                            ->numeric()
                                            ->prefix('৳')
                                            ->default(0),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->label('স্ট্যাটাস')
                                            ->options([
                                                'active' => 'সক্রিয়',
                                                'inactive' => 'নিষ্ক্রিয়',
                                                'on_leave' => 'ছুটিতে',
                                                'resigned' => 'পদত্যাগ',
                                                'retired' => 'অবসরপ্রাপ্ত',
                                                'terminated' => 'চাকরিচ্যুত',
                                            ])
                                            ->default('active')
                                            ->required()
                                            ->native(false),

                                        Forms\Components\DatePicker::make('resignation_date')
                                            ->label('পদত্যাগ/অবসরের তারিখ')
                                            ->native(false)
                                            ->visible(fn(Forms\Get $get) => in_array($get('status'), ['resigned', 'retired', 'terminated'])),
                                    ]),
                            ]),

                        // Tab 4: Education
                        Forms\Components\Tabs\Tab::make('শিক্ষাগত যোগ্যতা')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Forms\Components\Repeater::make('education')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\Grid::make(4)
                                            ->schema([
                                                Forms\Components\TextInput::make('degree')
                                                    ->label('ডিগ্রি/সার্টিফিকেট')
                                                    ->placeholder('যেমন: দাওরা হাদিস, ফাজিল, কামিল')
                                                    ->required(),

                                                Forms\Components\TextInput::make('institution')
                                                    ->label('প্রতিষ্ঠান')
                                                    ->placeholder('মাদরাসা/বিশ্ববিদ্যালয়ের নাম')
                                                    ->required(),

                                                Forms\Components\TextInput::make('passing_year')
                                                    ->label('পাসের সন')
                                                    ->numeric()
                                                    ->minValue(1950)
                                                    ->maxValue(date('Y')),

                                                Forms\Components\TextInput::make('result')
                                                    ->label('ফলাফল/GPA')
                                                    ->placeholder('যেমন: মুমতাজ, জয়্যিদ'),
                                            ]),
                                    ])
                                    ->defaultItems(1)
                                    ->addActionLabel('আরও যোগ করুন')
                                    ->reorderable()
                                    ->collapsible()
                                    ->columnSpanFull(),
                            ]),

                        // Tab 5: Experience
                        Forms\Components\Tabs\Tab::make('অভিজ্ঞতা')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\Repeater::make('experience')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\Grid::make(4)
                                            ->schema([
                                                Forms\Components\TextInput::make('organization')
                                                    ->label('প্রতিষ্ঠান')
                                                    ->required(),

                                                Forms\Components\TextInput::make('position')
                                                    ->label('পদবী')
                                                    ->required(),

                                                Forms\Components\DatePicker::make('from_date')
                                                    ->label('শুরু')
                                                    ->native(false)
                                                    ->required(),

                                                Forms\Components\DatePicker::make('to_date')
                                                    ->label('শেষ')
                                                    ->native(false),
                                            ]),

                                        Forms\Components\Textarea::make('responsibilities')
                                            ->label('দায়িত্ব')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('অভিজ্ঞতা যোগ করুন')
                                    ->reorderable()
                                    ->collapsible()
                                    ->columnSpanFull(),
                            ]),

                        // Tab 6: Other
                        Forms\Components\Tabs\Tab::make('অন্যান্য')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Textarea::make('specialization')
                                    ->label('বিশেষত্ব/দক্ষতা')
                                    ->placeholder('যেমন: হাদিস, তাফসীর, ফিকহ, কুরআন তিলাওয়াত')
                                    ->rows(2),

                                Forms\Components\Textarea::make('notes')
                                    ->label('মন্তব্য/নোট')
                                    ->rows(2),

                                Forms\Components\FileUpload::make('documents')
                                    ->label('ডকুমেন্টস')
                                    ->multiple()
                                    ->directory('teachers/documents')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->maxSize(5120)
                                    ->downloadable(),
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
                Tables\Columns\ImageColumn::make('photo')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn() => asset('images/default-teacher.png'))
                    ->size(40),

                Tables\Columns\TextColumn::make('employee_id')
                    ->label('আইডি')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('name')
                    ->label('নাম')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn(Teacher $record): string => $record->designation?->name ?? ''),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('বিভাগ')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('মোবাইল')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->copyable(),

                Tables\Columns\TextColumn::make('joining_date')
                    ->label('যোগদান')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('basic_salary')
                    ->label('বেতন')
                    ->money('BDT')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('employment_type')
                    ->label('ধরণ')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'permanent' => 'success',
                        'temporary' => 'warning',
                        'contractual' => 'info',
                        'part_time' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'permanent' => 'স্থায়ী',
                        'temporary' => 'অস্থায়ী',
                        'contractual' => 'চুক্তি',
                        'part_time' => 'পার্ট-টাইম',
                        default => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'on_leave' => 'info',
                        'resigned', 'retired', 'terminated' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'সক্রিয়',
                        'inactive' => 'নিষ্ক্রিয়',
                        'on_leave' => 'ছুটিতে',
                        'resigned' => 'পদত্যাগ',
                        'retired' => 'অবসর',
                        'terminated' => 'চ্যুত',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department_id')
                    ->label('বিভাগ')
                    ->relationship('department', 'name')
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('designation_id')
                    ->label('পদবী')
                    ->relationship('designation', 'name')
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options([
                        'active' => 'সক্রিয়',
                        'inactive' => 'নিষ্ক্রিয়',
                        'on_leave' => 'ছুটিতে',
                        'resigned' => 'পদত্যাগ',
                        'retired' => 'অবসর',
                    ]),

                Tables\Filters\SelectFilter::make('employment_type')
                    ->label('চাকরির ধরণ')
                    ->options([
                        'permanent' => 'স্থায়ী',
                        'temporary' => 'অস্থায়ী',
                        'contractual' => 'চুক্তি',
                        'part_time' => 'পার্ট-টাইম',
                    ]),

                Tables\Filters\TrashedFilter::make()
                    ->label('মুছে ফেলা'),
            ])
            ->filtersFormColumns(2)
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('দেখুন'),
                    Tables\Actions\EditAction::make()
                        ->label('সম্পাদনা'),
                    Tables\Actions\Action::make('idCard')
                        ->label('আইডি কার্ড')
                        ->icon('heroicon-o-identification')
                        ->url(fn(Teacher $record): string => route('teacher.id-card', $record))
                        ->openUrlInNewTab(),
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
                                    'on_leave' => 'ছুটিতে',
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(fn(Teacher $record) => $record->update(['status' => $data['status']]));
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('কোন শিক্ষক নেই')
            ->emptyStateDescription('নতুন শিক্ষক যোগ করতে নিচের বাটনে ক্লিক করুন')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\ImageEntry::make('photo')
                                    ->label('')
                                    ->circular()
                                    ->defaultImageUrl(fn() => asset('images/default-teacher.png'))
                                    ->size(100),

                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('name')
                                        ->label('নাম')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold),
                                    Infolists\Components\TextEntry::make('employee_id')
                                        ->label('কর্মচারী আইডি')
                                        ->badge()
                                        ->color('primary'),
                                    Infolists\Components\TextEntry::make('designation.name')
                                        ->label('পদবী'),
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
                                        Infolists\Components\TextEntry::make('mother_name')
                                            ->label('মাতার নাম'),
                                        Infolists\Components\TextEntry::make('date_of_birth')
                                            ->label('জন্ম তারিখ')
                                            ->date('d M Y'),
                                        Infolists\Components\TextEntry::make('gender')
                                            ->label('লিঙ্গ')
                                            ->formatStateUsing(fn(string $state): string => $state === 'male' ? 'পুরুষ' : 'মহিলা'),
                                        Infolists\Components\TextEntry::make('marital_status')
                                            ->label('বৈবাহিক অবস্থা'),
                                        Infolists\Components\TextEntry::make('blood_group')
                                            ->label('রক্তের গ্রুপ')
                                            ->badge(),
                                        Infolists\Components\TextEntry::make('nid')
                                            ->label('NID'),
                                        Infolists\Components\TextEntry::make('phone')
                                            ->label('মোবাইল')
                                            ->icon('heroicon-o-phone'),
                                        Infolists\Components\TextEntry::make('email')
                                            ->label('ইমেইল')
                                            ->icon('heroicon-o-envelope'),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make('চাকরি সংক্রান্ত')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Infolists\Components\Grid::make(3)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('department.name')
                                            ->label('বিভাগ')
                                            ->badge()
                                            ->color('info'),
                                        Infolists\Components\TextEntry::make('designation.name')
                                            ->label('পদবী'),
                                        Infolists\Components\TextEntry::make('joining_date')
                                            ->label('যোগদান')
                                            ->date('d M Y'),
                                        Infolists\Components\TextEntry::make('employment_type')
                                            ->label('ধরণ')
                                            ->badge(),
                                        Infolists\Components\TextEntry::make('basic_salary')
                                            ->label('মূল বেতন')
                                            ->money('BDT'),
                                        Infolists\Components\TextEntry::make('status')
                                            ->label('স্ট্যাটাস')
                                            ->badge()
                                            ->color(fn(string $state): string => $state === 'active' ? 'success' : 'warning'),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make('শিক্ষাগত যোগ্যতা')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('education')
                                    ->label('')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('degree')
                                            ->label('ডিগ্রি'),
                                        Infolists\Components\TextEntry::make('institution')
                                            ->label('প্রতিষ্ঠান'),
                                        Infolists\Components\TextEntry::make('passing_year')
                                            ->label('সন'),
                                        Infolists\Components\TextEntry::make('result')
                                            ->label('ফলাফল'),
                                    ])
                                    ->columns(4),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'view' => Pages\ViewTeacher::route('/{record}'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
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
        return 'info';
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'বিভাগ' => $record->department?->name,
            'পদবী' => $record->designation?->name,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'name_en', 'employee_id', 'phone'];
    }
}
