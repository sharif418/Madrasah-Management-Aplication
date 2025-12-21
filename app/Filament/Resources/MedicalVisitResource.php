<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicalVisitResource\Pages;
use App\Models\MedicalVisit;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MedicalVisitResource extends BaseResource
{
    protected static ?string $model = MedicalVisit::class;

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';

    protected static ?string $navigationGroup = 'স্বাস্থ্য ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'সিক রুম/ভিজিট';

    protected static ?string $modelLabel = 'মেডিকেল ভিজিট';

    protected static ?string $pluralModelLabel = 'মেডিকেল ভিজিট';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ভিজিট তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('student_id')
                                    ->label('ছাত্র')
                                    ->options(Student::where('status', 'active')
                                        ->get()
                                        ->mapWithKeys(fn($s) => [$s->id => "{$s->student_id} - {$s->name}"]))
                                    ->required()
                                    ->searchable()
                                    ->native(false),

                                Forms\Components\DateTimePicker::make('visit_date')
                                    ->label('তারিখ ও সময়')
                                    ->default(now())
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('visit_type')
                                    ->label('ভিজিটের ধরণ')
                                    ->options(MedicalVisit::visitTypeOptions())
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Textarea::make('symptoms')
                            ->label('উপসর্গ/অভিযোগ')
                            ->rows(2),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Textarea::make('diagnosis')
                                    ->label('রোগ নির্ণয়')
                                    ->rows(2),

                                Forms\Components\Textarea::make('treatment')
                                    ->label('চিকিৎসা')
                                    ->rows(2),
                            ]),
                    ]),

                Forms\Components\Section::make('ওষুধ ও রেফারেল')
                    ->schema([
                        Forms\Components\TagsInput::make('medicines_given')
                            ->label('প্রদত্ত ওষুধ')
                            ->placeholder('ওষুধের নাম লিখুন'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('referred_to')
                                    ->label('রেফার করা হয়েছে')
                                    ->placeholder('হাসপাতাল/ডাক্তার'),

                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(MedicalVisit::statusOptions())
                                    ->default('treated')
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Textarea::make('doctor_notes')
                            ->label('ডাক্তারের নোট')
                            ->rows(2),
                    ]),

                Forms\Components\Section::make('অভিভাবক ও ফলো-আপ')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('parent_informed')
                                    ->label('অভিভাবককে জানানো হয়েছে')
                                    ->live(),

                                Forms\Components\DateTimePicker::make('parent_informed_date')
                                    ->label('জানানোর সময়')
                                    ->native(false)
                                    ->visible(fn(Forms\Get $get) => $get('parent_informed')),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('follow_up_required')
                                    ->label('ফলো-আপ প্রয়োজন')
                                    ->live(),

                                Forms\Components\DatePicker::make('follow_up_date')
                                    ->label('ফলো-আপ তারিখ')
                                    ->native(false)
                                    ->visible(fn(Forms\Get $get) => $get('follow_up_required')),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('visit_date')
                    ->label('তারিখ')
                    ->dateTime('d M Y h:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('ছাত্র')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('visit_type')
                    ->label('ধরণ')
                    ->formatStateUsing(fn($state) => MedicalVisit::visitTypeOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn($state) => $state === 'emergency' ? 'danger' : 'info'),

                Tables\Columns\TextColumn::make('symptoms')
                    ->label('উপসর্গ')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->formatStateUsing(fn($state) => MedicalVisit::statusOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'treated' => 'success',
                        'referred' => 'warning',
                        'sent_home' => 'info',
                        'hospitalized' => 'danger',
                        'recovered' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('parent_informed')
                    ->label('অভিভাবক')
                    ->boolean(),

                Tables\Columns\IconColumn::make('follow_up_required')
                    ->label('ফলো-আপ')
                    ->boolean()
                    ->trueIcon('heroicon-o-clock')
                    ->falseIcon('heroicon-o-check'),
            ])
            ->defaultSort('visit_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('visit_type')
                    ->label('ধরণ')
                    ->options(MedicalVisit::visitTypeOptions()),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(MedicalVisit::statusOptions()),

                Tables\Filters\Filter::make('today')
                    ->label('আজকের ভিজিট')
                    ->query(fn(Builder $query) => $query->today()),

                Tables\Filters\Filter::make('follow_up')
                    ->label('ফলো-আপ বাকি')
                    ->query(fn(Builder $query) => $query->requiringFollowUp()),
            ])
            ->actions([
                Tables\Actions\Action::make('informParent')
                    ->label('অভিভাবক জানান')
                    ->icon('heroicon-o-phone')
                    ->color('warning')
                    ->action(fn($record) => $record->update([
                        'parent_informed' => true,
                        'parent_informed_date' => now(),
                    ]))
                    ->visible(fn($record) => !$record->parent_informed)
                    ->requiresConfirmation(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('কোন মেডিকেল ভিজিট নেই')
            ->emptyStateIcon('heroicon-o-plus-circle');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedicalVisits::route('/'),
            'create' => Pages\CreateMedicalVisit::route('/create'),
            'edit' => Pages\EditMedicalVisit::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::today()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
