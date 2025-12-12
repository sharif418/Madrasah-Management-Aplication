<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffResource\Pages;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'শিক্ষক ও স্টাফ';

    protected static ?string $modelLabel = 'কর্মচারী';

    protected static ?string $pluralModelLabel = 'কর্মচারী';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ব্যক্তিগত তথ্য')
                    ->description('কর্মচারীর মূল তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('employee_id')
                                    ->label('কর্মচারী আইডি')
                                    ->default(fn() => Staff::generateEmployeeId())
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('name')
                                    ->label('নাম (বাংলা)')
                                    ->required(),

                                Forms\Components\TextInput::make('name_en')
                                    ->label('নাম (English)'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('father_name')
                                    ->label('পিতার নাম'),

                                Forms\Components\Select::make('gender')
                                    ->label('লিঙ্গ')
                                    ->options([
                                        'male' => 'পুরুষ',
                                        'female' => 'মহিলা',
                                    ])
                                    ->default('male')
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->label('জন্ম তারিখ')
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

                                Forms\Components\Select::make('religion')
                                    ->label('ধর্ম')
                                    ->options([
                                        'islam' => 'ইসলাম',
                                        'hinduism' => 'হিন্দু',
                                        'christianity' => 'খ্রিস্টান',
                                        'buddhism' => 'বৌদ্ধ',
                                        'other' => 'অন্যান্য',
                                    ])
                                    ->default('islam')
                                    ->native(false),
                            ]),
                    ]),

                Forms\Components\Section::make('যোগাযোগ')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->label('মোবাইল')
                                    ->tel()
                                    ->required(),

                                Forms\Components\TextInput::make('email')
                                    ->label('ইমেইল')
                                    ->email(),

                                Forms\Components\TextInput::make('nid')
                                    ->label('জাতীয় পরিচয়পত্র'),
                            ]),

                        Forms\Components\Textarea::make('address')
                            ->label('ঠিকানা')
                            ->rows(2),
                    ]),

                Forms\Components\Section::make('কর্মসংস্থান')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('designation_id')
                                    ->label('পদবী')
                                    ->relationship('designation', 'title')
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('title')
                                            ->label('পদবী')
                                            ->required(),
                                    ]),

                                Forms\Components\Select::make('employment_type')
                                    ->label('কর্মসংস্থানের ধরণ')
                                    ->options([
                                        'permanent' => 'স্থায়ী',
                                        'temporary' => 'অস্থায়ী',
                                        'contractual' => 'চুক্তিভিত্তিক',
                                        'part_time' => 'পার্ট-টাইম',
                                    ])
                                    ->default('permanent')
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('joining_date')
                                    ->label('যোগদানের তারিখ')
                                    ->default(now())
                                    ->native(false),

                                Forms\Components\TextInput::make('basic_salary')
                                    ->label('মূল বেতন')
                                    ->numeric()
                                    ->prefix('৳'),

                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options([
                                        'active' => 'সক্রিয়',
                                        'inactive' => 'নিষ্ক্রিয়',
                                        'terminated' => 'চাকরিচ্যুত',
                                        'resigned' => 'পদত্যাগী',
                                    ])
                                    ->default('active')
                                    ->native(false),
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->label('মন্তব্য')
                            ->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id')
                    ->label('আইডি')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('name')
                    ->label('নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('designation.title')
                    ->label('পদবী')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('মোবাইল')
                    ->searchable(),

                Tables\Columns\TextColumn::make('employment_type')
                    ->label('ধরণ')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'permanent' => 'স্থায়ী',
                        'temporary' => 'অস্থায়ী',
                        'contractual' => 'চুক্তি',
                        'part_time' => 'পার্ট-টাইম',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('basic_salary')
                    ->label('বেতন')
                    ->money('BDT')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('joining_date')
                    ->label('যোগদান')
                    ->date('d M Y')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'active' => 'সক্রিয়',
                        'inactive' => 'নিষ্ক্রিয়',
                        'terminated' => 'চাকরিচ্যুত',
                        'resigned' => 'পদত্যাগী',
                        default => $state,
                    })
                    ->color(fn($state) => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'terminated' => 'danger',
                        'resigned' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('designation_id')
                    ->label('পদবী')
                    ->relationship('designation', 'title'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options([
                        'active' => 'সক্রিয়',
                        'inactive' => 'নিষ্ক্রিয়',
                        'terminated' => 'চাকরিচ্যুত',
                        'resigned' => 'পদত্যাগী',
                    ]),

                Tables\Filters\SelectFilter::make('employment_type')
                    ->label('ধরণ')
                    ->options([
                        'permanent' => 'স্থায়ী',
                        'temporary' => 'অস্থায়ী',
                        'contractual' => 'চুক্তিভিত্তিক',
                        'part_time' => 'পার্ট-টাইম',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('কোন কর্মচারী নেই')
            ->emptyStateDescription('নতুন কর্মচারী যোগ করতে উপরের বাটনে ক্লিক করুন')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count() ?: null;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }
}
