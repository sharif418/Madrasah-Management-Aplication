<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceResource extends BaseResource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'উপস্থিতি ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'উপস্থিতি';

    protected static ?string $pluralModelLabel = 'উপস্থিতি রেকর্ড';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('উপস্থিতির তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date')
                                    ->label('তারিখ')
                                    ->required()
                                    ->default(now())
                                    ->native(false),

                                Forms\Components\Select::make('academic_year_id')
                                    ->label('শিক্ষাবর্ষ')
                                    ->relationship('academicYear', 'name')
                                    ->default(fn() => AcademicYear::current()?->id)
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('class_id')
                                    ->label('শ্রেণি')
                                    ->relationship('class', 'name', fn(Builder $query) => $query->where('is_active', true))
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(fn(Forms\Set $set) => $set('section_id', null)),

                                Forms\Components\Select::make('section_id')
                                    ->label('শাখা')
                                    ->options(function (Forms\Get $get) {
                                        $classId = $get('class_id');
                                        if (!$classId)
                                            return [];
                                        return \App\Models\Section::where('class_id', $classId)
                                            ->where('is_active', true)
                                            ->pluck('name', 'id');
                                    })
                                    ->native(false),

                                Forms\Components\Select::make('student_id')
                                    ->label('ছাত্র')
                                    ->options(function (Forms\Get $get) {
                                        $classId = $get('class_id');
                                        $sectionId = $get('section_id');
                                        if (!$classId)
                                            return [];

                                        $query = \App\Models\Student::where('class_id', $classId)
                                            ->where('status', 'active');

                                        if ($sectionId) {
                                            $query->where('section_id', $sectionId);
                                        }

                                        return $query->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(Attendance::statusOptions())
                                    ->default('present')
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                Forms\Components\TimePicker::make('in_time')
                                    ->label('প্রবেশের সময়')
                                    ->seconds(false)
                                    ->native(false)
                                    ->visible(fn(Forms\Get $get) => in_array($get('status'), ['present', 'late', 'half_day'])),

                                Forms\Components\TimePicker::make('out_time')
                                    ->label('বের হওয়ার সময়')
                                    ->seconds(false)
                                    ->native(false)
                                    ->visible(fn(Forms\Get $get) => in_array($get('status'), ['present', 'late', 'half_day'])),
                            ]),

                        Forms\Components\Textarea::make('remarks')
                            ->label('মন্তব্য')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('ছাত্রের নাম')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('student.student_id')
                    ->label('ছাত্র আইডি')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('class.name')
                    ->label('শ্রেণি')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('section.name')
                    ->label('শাখা')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->color(fn(string $state): string => Attendance::statusColors()[$state] ?? 'gray')
                    ->formatStateUsing(fn(string $state): string => Attendance::statusOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('in_time')
                    ->label('প্রবেশ')
                    ->time('h:i A')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('out_time')
                    ->label('প্রস্থান')
                    ->time('h:i A')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_late')
                    ->label('দেরি')
                    ->boolean()
                    ->trueIcon('heroicon-o-clock')
                    ->trueColor('warning')
                    ->falseIcon('')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('remarks')
                    ->label('মন্তব্য')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('markedBy.name')
                    ->label('গ্রহণকারী')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date')
                            ->label('তারিখ')
                            ->default(now())
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['date'],
                            fn(Builder $query, $date): Builder => $query->whereDate('date', $date),
                        );
                    }),

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('শ্রেণি')
                    ->relationship('class', 'name')
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('section_id')
                    ->label('শাখা')
                    ->relationship('section', 'name')
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(Attendance::statusOptions()),

                Tables\Filters\TernaryFilter::make('late_only')
                    ->label('শুধু দেরি')
                    ->queries(
                        true: fn(Builder $query) => $query->where('status', 'late'),
                        false: fn(Builder $query) => $query->where('status', '!=', 'late'),
                    ),

                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('শিক্ষাবর্ষ')
                    ->relationship('academicYear', 'name')
                    ->default(fn() => AcademicYear::current()?->id)
                    ->preload(),
            ])
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('দেখুন'),
                Tables\Actions\EditAction::make()
                    ->label('সম্পাদনা'),
                Tables\Actions\DeleteAction::make()
                    ->label('মুছুন'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('markPresent')
                        ->label('উপস্থিত করুন')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn($records) => $records->each->update(['status' => 'present']))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('markAbsent')
                        ->label('অনুপস্থিত করুন')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn($records) => $records->each->update(['status' => 'absent']))
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('date', 'desc')
            ->striped()
            ->emptyStateHeading('উপস্থিতি রেকর্ড নেই')
            ->emptyStateDescription('উপস্থিতি রেকর্ড করতে বাল্ক এন্ট্রি পেজ ব্যবহার করুন')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
            'bulk-entry' => Pages\BulkAttendanceEntry::route('/bulk-entry'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('date', today())->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
