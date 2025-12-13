<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SyllabusResource\Pages;
use App\Models\Syllabus;
use App\Models\AcademicYear;
use App\Models\ClassName;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SyllabusResource extends Resource
{
    protected static ?string $model = Syllabus::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'একাডেমিক সেটআপ';

    protected static ?string $navigationLabel = 'সিলেবাস';

    protected static ?string $modelLabel = 'সিলেবাস';

    protected static ?string $pluralModelLabel = 'সিলেবাস';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('সিলেবাস তথ্য')
                    ->description('ক্লাস ও বিষয় অনুযায়ী সিলেবাস আপলোড করুন')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('academic_year_id')
                                    ->label('শিক্ষাবর্ষ')
                                    ->options(AcademicYear::orderBy('name', 'desc')->pluck('name', 'id'))
                                    ->default(fn() => AcademicYear::where('is_current', true)->first()?->id)
                                    ->required()
                                    ->native(false)
                                    ->searchable(),

                                Forms\Components\Select::make('class_id')
                                    ->label('শ্রেণি')
                                    ->options(ClassName::where('is_active', true)->orderBy('order')->pluck('name', 'id'))
                                    ->required()
                                    ->native(false)
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn(callable $set) => $set('subject_id', null)),

                                Forms\Components\Select::make('subject_id')
                                    ->label('বিষয় (ঐচ্ছিক)')
                                    ->options(function (Forms\Get $get) {
                                        $classId = $get('class_id');
                                        if (!$classId) {
                                            return Subject::where('is_active', true)->pluck('name', 'id');
                                        }
                                        return Subject::whereHas('classes', fn($q) => $q->where('class_id', $classId))
                                            ->orWhere('is_active', true)
                                            ->pluck('name', 'id');
                                    })
                                    ->native(false)
                                    ->searchable()
                                    ->placeholder('সম্পূর্ণ ক্লাস সিলেবাস'),
                            ]),

                        Forms\Components\TextInput::make('title')
                            ->label('সিলেবাস শিরোনাম')
                            ->placeholder('যেমন: বার্ষিক সিলেবাস ২০২৫, প্রথম সাময়িক সিলেবাস')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('বিবরণ')
                            ->placeholder('সিলেবাস সম্পর্কে অতিরিক্ত তথ্য')
                            ->rows(2),
                    ]),

                Forms\Components\Section::make('ফাইল আপলোড')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('সিলেবাস ফাইল')
                            ->directory('syllabi')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(10240) // 10MB
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->helperText('PDF বা ইমেজ ফাইল আপলোড করুন (সর্বোচ্চ ১০MB)'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('সক্রিয়')
                            ->default(true)
                            ->helperText('নিষ্ক্রিয় সিলেবাস ছাত্রদের কাছে দেখাবে না'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('শিক্ষাবর্ষ')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('class.name')
                    ->label('শ্রেণি')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('বিষয়')
                    ->default('সম্পূর্ণ ক্লাস')
                    ->badge()
                    ->color(fn($state) => $state === 'সম্পূর্ণ ক্লাস' ? 'success' : 'primary'),

                Tables\Columns\TextColumn::make('title')
                    ->label('শিরোনাম')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\IconColumn::make('file_path')
                    ->label('ফাইল')
                    ->icon(fn($state) => $state ? 'heroicon-o-document-arrow-down' : 'heroicon-o-x-mark')
                    ->color(fn($state) => $state ? 'success' : 'danger'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('তৈরি')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('শিক্ষাবর্ষ')
                    ->options(AcademicYear::orderBy('name', 'desc')->pluck('name', 'id'))
                    ->default(fn() => AcademicYear::where('is_current', true)->first()?->id),

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('শ্রেণি')
                    ->options(ClassName::where('is_active', true)->orderBy('order')->pluck('name', 'id')),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('স্ট্যাটাস')
                    ->boolean()
                    ->trueLabel('সক্রিয়')
                    ->falseLabel('নিষ্ক্রিয়'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('ডাউনলোড')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn(Syllabus $record) => $record->file_path ? asset('storage/' . $record->file_path) : null)
                    ->openUrlInNewTab()
                    ->visible(fn(Syllabus $record) => $record->file_path),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('কোন সিলেবাস নেই')
            ->emptyStateDescription('নতুন সিলেবাস আপলোড করতে উপরের বাটনে ক্লিক করুন')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSyllabi::route('/'),
            'create' => Pages\CreateSyllabus::route('/create'),
            'edit' => Pages\EditSyllabus::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count() ?: null;
    }
}
