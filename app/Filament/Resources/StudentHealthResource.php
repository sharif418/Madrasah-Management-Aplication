<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentHealthResource\Pages;
use App\Models\StudentHealth;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentHealthResource extends BaseResource
{
    protected static ?string $model = StudentHealth::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationGroup = 'স্বাস্থ্য ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'স্বাস্থ্য প্রোফাইল';

    protected static ?string $modelLabel = 'স্বাস্থ্য প্রোফাইল';

    protected static ?string $pluralModelLabel = 'স্বাস্থ্য প্রোফাইল';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('স্বাস্থ্য প্রোফাইল')
                    ->tabs([
                        // Tab 1: Basic
                        Forms\Components\Tabs\Tab::make('মূল তথ্য')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Select::make('student_id')
                                    ->label('ছাত্র')
                                    ->options(Student::where('status', 'active')
                                        ->get()
                                        ->mapWithKeys(fn($s) => [$s->id => "{$s->student_id} - {$s->name}"]))
                                    ->required()
                                    ->searchable()
                                    ->unique(ignoreRecord: true)
                                    ->native(false),

                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\TextInput::make('height')
                                            ->label('উচ্চতা (সে.মি.)')
                                            ->numeric()
                                            ->suffix('cm'),

                                        Forms\Components\TextInput::make('weight')
                                            ->label('ওজন (কেজি)')
                                            ->numeric()
                                            ->suffix('kg'),

                                        Forms\Components\Select::make('blood_group')
                                            ->label('রক্তের গ্রুপ')
                                            ->options(StudentHealth::bloodGroupOptions())
                                            ->native(false),

                                        Forms\Components\Placeholder::make('bmi')
                                            ->label('BMI')
                                            ->content(fn(?StudentHealth $record) => $record?->bmi
                                                ? "{$record->bmi} ({$record->bmi_category})"
                                                : 'N/A'),
                                    ]),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('vision_left')
                                            ->label('বাম চোখ')
                                            ->placeholder('যেমন: 6/6'),

                                        Forms\Components\TextInput::make('vision_right')
                                            ->label('ডান চোখ')
                                            ->placeholder('যেমন: 6/6'),

                                        Forms\Components\Select::make('hearing_status')
                                            ->label('শ্রবণ শক্তি')
                                            ->options(StudentHealth::hearingOptions())
                                            ->default('normal')
                                            ->native(false),
                                    ]),
                            ]),

                        // Tab 2: Medical Conditions
                        Forms\Components\Tabs\Tab::make('চিকিৎসা তথ্য')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Forms\Components\TagsInput::make('allergies')
                                    ->label('এলার্জি')
                                    ->placeholder('এলার্জি যোগ করুন')
                                    ->suggestions(StudentHealth::commonAllergies()),

                                Forms\Components\TagsInput::make('chronic_conditions')
                                    ->label('দীর্ঘমেয়াদী রোগ')
                                    ->placeholder('যেমন: হাঁপানি, ডায়াবেটিস'),

                                Forms\Components\Textarea::make('disabilities')
                                    ->label('প্রতিবন্ধকতা')
                                    ->rows(1),

                                Forms\Components\TagsInput::make('current_medications')
                                    ->label('বর্তমান ওষুধ')
                                    ->placeholder('নিয়মিত ওষুধ'),

                                Forms\Components\TagsInput::make('past_surgeries')
                                    ->label('অতীত অস্ত্রোপচার'),

                                Forms\Components\Textarea::make('family_medical_history')
                                    ->label('পারিবারিক ইতিহাস')
                                    ->rows(1),
                            ]),

                        // Tab 3: Immunization
                        Forms\Components\Tabs\Tab::make('টিকা ও পরীক্ষা')
                            ->icon('heroicon-o-beaker')
                            ->schema([
                                Forms\Components\KeyValue::make('immunization_records')
                                    ->label('টিকার রেকর্ড')
                                    ->keyLabel('টিকার নাম')
                                    ->valueLabel('তারিখ')
                                    ->addActionLabel('টিকা যোগ করুন'),

                                Forms\Components\DatePicker::make('last_physical_exam')
                                    ->label('শেষ শারীরিক পরীক্ষা')
                                    ->native(false),
                            ]),

                        // Tab 4: Emergency
                        Forms\Components\Tabs\Tab::make('জরুরি যোগাযোগ')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('doctor_name')
                                            ->label('ডাক্তারের নাম'),

                                        Forms\Components\TextInput::make('doctor_phone')
                                            ->label('ডাক্তারের ফোন')
                                            ->tel(),
                                    ]),

                                Forms\Components\TextInput::make('emergency_hospital')
                                    ->label('জরুরি হাসপাতাল'),

                                Forms\Components\Textarea::make('insurance_info')
                                    ->label('বীমা তথ্য')
                                    ->rows(1),

                                Forms\Components\Textarea::make('special_dietary_needs')
                                    ->label('বিশেষ খাদ্য প্রয়োজন')
                                    ->rows(1),

                                Forms\Components\Textarea::make('notes')
                                    ->label('মন্তব্য')
                                    ->rows(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.student_id')
                    ->label('আইডি')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('blood_group')
                    ->label('রক্ত')
                    ->badge()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('height')
                    ->label('উচ্চতা')
                    ->suffix(' cm')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('weight')
                    ->label('ওজন')
                    ->suffix(' kg')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('bmi')
                    ->label('BMI')
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state < 18.5 => 'warning',
                        $state < 25 => 'success',
                        $state < 30 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('allergies')
                    ->label('এলার্জি')
                    ->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : '-')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('last_physical_exam')
                    ->label('শেষ পরীক্ষা')
                    ->date('d M Y')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('blood_group')
                    ->label('রক্তের গ্রুপ')
                    ->options(StudentHealth::bloodGroupOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('কোন স্বাস্থ্য প্রোফাইল নেই')
            ->emptyStateIcon('heroicon-o-heart');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentHealths::route('/'),
            'create' => Pages\CreateStudentHealth::route('/create'),
            'edit' => Pages\EditStudentHealth::route('/{record}/edit'),
        ];
    }
}
