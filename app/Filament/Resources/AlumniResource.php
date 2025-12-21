<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlumniResource\Pages;
use App\Models\Alumni;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;

class AlumniResource extends BaseResource
{
    protected static ?string $model = Alumni::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'প্রাক্তন ছাত্র';

    protected static ?string $modelLabel = 'প্রাক্তন ছাত্র';

    protected static ?string $pluralModelLabel = 'প্রাক্তন ছাত্রগণ';

    protected static ?string $navigationGroup = 'ছাত্র ব্যবস্থাপনা';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('প্রাক্তন ছাত্রের তথ্য')
                    ->description('সাবেক ছাত্রের বিস্তারিত তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('student_id')
                                    ->label('ছাত্র (যদি রেকর্ড থাকে)')
                                    ->relationship('student', 'name', fn(Builder $query) =>
                                        $query->where('status', 'passed_out'))
                                    ->searchable()
                                    ->preload()
                                    ->helperText('পাস করা ছাত্রদের তালিকা থেকে নির্বাচন করুন')
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        if ($state) {
                                            $student = Student::find($state);
                                            if ($student) {
                                                $set('name', $student->name);
                                                $set('phone', $student->father_phone);
                                                $set('email', $student->email);
                                            }
                                        }
                                    }),

                                Forms\Components\TextInput::make('passing_year')
                                    ->label('পাস করার সাল')
                                    ->numeric()
                                    ->minValue(1980)
                                    ->maxValue(date('Y'))
                                    ->default(date('Y'))
                                    ->required(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('নাম')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('last_class')
                                    ->label('সর্বশেষ শ্রেণি')
                                    ->placeholder('যেমন: তাকমীল, ফজিলত ২য় বর্ষ')
                                    ->maxLength(100),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->label('মোবাইল')
                                    ->tel()
                                    ->prefix('+880')
                                    ->maxLength(15),

                                Forms\Components\TextInput::make('email')
                                    ->label('ইমেইল')
                                    ->email()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\TextInput::make('current_occupation')
                            ->label('বর্তমান পেশা')
                            ->placeholder('যেমন: ইমাম, মুহাদ্দিস, শিক্ষক, ব্যবসায়ী')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('current_address')
                            ->label('বর্তমান ঠিকানা')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('achievements')
                            ->label('অর্জন/সাফল্য')
                            ->placeholder('উল্লেখযোগ্য কোন অর্জন থাকলে লিখুন...')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('photo')
                            ->label('ছবি')
                            ->image()
                            ->directory('alumni/photos')
                            ->avatar()
                            ->circleCropper(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn() => asset('images/default-student.png'))
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                    ->label('নাম')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('passing_year')
                    ->label('পাস সাল')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('last_class')
                    ->label('সর্বশেষ শ্রেণি')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('current_occupation')
                    ->label('বর্তমান পেশা')
                    ->searchable()
                    ->placeholder('-')
                    ->limit(30),

                Tables\Columns\TextColumn::make('phone')
                    ->label('মোবাইল')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->copyable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('যোগ করা হয়েছে')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('passing_year')
                    ->label('পাস সাল')
                    ->options(function () {
                        $years = [];
                        for ($y = date('Y'); $y >= 2000; $y--) {
                            $years[$y] = $y;
                        }
                        return $years;
                    }),
            ])
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
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportPdf')
                    ->label('PDF রিপোর্ট')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('year')
                            ->label('বছর নির্বাচন')
                            ->options(function () {
                                $years = ['all' => 'সব বছর'];
                                for ($y = date('Y'); $y >= 2000; $y--) {
                                    $years[$y] = $y;
                                }
                                return $years;
                            })
                            ->default('all'),
                    ])
                    ->action(function (array $data) {
                        $query = Alumni::query()->orderBy('passing_year', 'desc');
                        if ($data['year'] !== 'all') {
                            $query->where('passing_year', $data['year']);
                        }
                        $alumni = $query->get();

                        $pdf = Pdf::loadView('pdf.alumni-list', [
                            'alumni' => $alumni,
                            'year' => $data['year'],
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'alumni-list-' . now()->format('Y-m-d') . '.pdf');
                    }),
            ])
            ->defaultSort('passing_year', 'desc')
            ->emptyStateHeading('কোন প্রাক্তন ছাত্র নেই')
            ->emptyStateDescription('প্রাক্তন ছাত্র যোগ করতে উপরের বাটনে ক্লিক করুন')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlumni::route('/'),
            'create' => Pages\CreateAlumni::route('/create'),
            'edit' => Pages\EditAlumni::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'gray';
    }
}
