<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisciplineIncidentResource\Pages;
use App\Models\DisciplineIncident;
use App\Models\Student;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DisciplineIncidentResource extends BaseResource
{
    protected static ?string $model = DisciplineIncident::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationGroup = 'শৃঙ্খলা ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'শৃঙ্খলা ঘটনা';

    protected static ?string $modelLabel = 'শৃঙ্খলা ঘটনা';

    protected static ?string $pluralModelLabel = 'শৃঙ্খলা ঘটনা';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('শৃঙ্খলা ঘটনা')
                    ->tabs([
                        // Tab 1: Basic Info
                        Forms\Components\Tabs\Tab::make('মূল তথ্য')
                            ->icon('heroicon-o-information-circle')
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

                                        Forms\Components\Select::make('academic_year_id')
                                            ->label('শিক্ষাবর্ষ')
                                            ->options(AcademicYear::orderBy('name', 'desc')->pluck('name', 'id'))
                                            ->default(fn() => AcademicYear::where('is_current', true)->first()?->id)
                                            ->required()
                                            ->native(false),

                                        Forms\Components\DatePicker::make('incident_date')
                                            ->label('ঘটনার তারিখ')
                                            ->required()
                                            ->default(now())
                                            ->native(false),
                                    ]),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('incident_type')
                                            ->label('ঘটনার ধরণ')
                                            ->options(DisciplineIncident::incidentTypeOptions())
                                            ->required()
                                            ->native(false),

                                        Forms\Components\Select::make('severity')
                                            ->label('গুরুত্ব')
                                            ->options(DisciplineIncident::severityOptions())
                                            ->required()
                                            ->native(false)
                                            ->live(),

                                        Forms\Components\TextInput::make('location')
                                            ->label('স্থান')
                                            ->placeholder('যেমন: ক্লাসরুম, মাঠ'),
                                    ]),

                                Forms\Components\Textarea::make('description')
                                    ->label('বিস্তারিত বিবরণ')
                                    ->required()
                                    ->rows(3),

                                Forms\Components\Textarea::make('witnesses')
                                    ->label('সাক্ষী (যদি থাকে)')
                                    ->rows(1),
                            ]),

                        // Tab 2: Action
                        Forms\Components\Tabs\Tab::make('ব্যবস্থা')
                            ->icon('heroicon-o-scale')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('action_taken')
                                            ->label('গৃহীত ব্যবস্থা')
                                            ->options(DisciplineIncident::actionOptions())
                                            ->native(false),

                                        Forms\Components\DatePicker::make('action_date')
                                            ->label('ব্যবস্থার তারিখ')
                                            ->native(false),

                                        Forms\Components\TextInput::make('merit_points')
                                            ->label('মেরিট পয়েন্ট (- কর্তন)')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('নেগেটিভ মান = পয়েন্ট কর্তন'),
                                    ]),

                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(DisciplineIncident::statusOptions())
                                    ->default('reported')
                                    ->required()
                                    ->native(false),

                                Forms\Components\Textarea::make('notes')
                                    ->label('মন্তব্য')
                                    ->rows(2),
                            ]),

                        // Tab 3: Parent Communication
                        Forms\Components\Tabs\Tab::make('অভিভাবক যোগাযোগ')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Toggle::make('parent_notified')
                                    ->label('অভিভাবককে জানানো হয়েছে')
                                    ->live(),

                                Forms\Components\DatePicker::make('parent_notified_date')
                                    ->label('জানানোর তারিখ')
                                    ->native(false)
                                    ->visible(fn(Forms\Get $get) => $get('parent_notified')),

                                Forms\Components\DatePicker::make('parent_meeting_date')
                                    ->label('সাক্ষাতের তারিখ')
                                    ->native(false),

                                Forms\Components\Textarea::make('parent_meeting_notes')
                                    ->label('সাক্ষাতের নোট')
                                    ->rows(2),
                            ]),

                        // Tab 4: Follow-up
                        Forms\Components\Tabs\Tab::make('ফলো-আপ')
                            ->icon('heroicon-o-arrow-path')
                            ->schema([
                                Forms\Components\Toggle::make('follow_up_required')
                                    ->label('ফলো-আপ প্রয়োজন')
                                    ->live(),

                                Forms\Components\DatePicker::make('follow_up_date')
                                    ->label('ফলো-আপ তারিখ')
                                    ->native(false)
                                    ->visible(fn(Forms\Get $get) => $get('follow_up_required')),

                                Forms\Components\Textarea::make('follow_up_notes')
                                    ->label('ফলো-আপ নোট')
                                    ->rows(2)
                                    ->visible(fn(Forms\Get $get) => $get('follow_up_required')),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('incident_date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('ছাত্র')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn($record) => $record->student?->student_id),

                Tables\Columns\TextColumn::make('incident_type')
                    ->label('ঘটনা')
                    ->formatStateUsing(fn($state) => DisciplineIncident::incidentTypeOptions()[$state] ?? $state)
                    ->wrap(),

                Tables\Columns\TextColumn::make('severity')
                    ->label('গুরুত্ব')
                    ->formatStateUsing(fn($state) => DisciplineIncident::severityOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'minor' => 'info',
                        'moderate' => 'warning',
                        'serious' => 'danger',
                        'severe' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('action_taken')
                    ->label('ব্যবস্থা')
                    ->formatStateUsing(fn($state) => DisciplineIncident::actionOptions()[$state] ?? $state)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('parent_notified')
                    ->label('অভিভাবক')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->formatStateUsing(fn($state) => DisciplineIncident::statusOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'reported' => 'warning',
                        'investigating' => 'info',
                        'action_taken' => 'primary',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('merit_points')
                    ->label('পয়েন্ট')
                    ->badge()
                    ->color(fn($state) => $state < 0 ? 'danger' : 'success')
                    ->toggleable(),
            ])
            ->defaultSort('incident_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('severity')
                    ->label('গুরুত্ব')
                    ->options(DisciplineIncident::severityOptions()),

                Tables\Filters\SelectFilter::make('incident_type')
                    ->label('ঘটনার ধরণ')
                    ->options(DisciplineIncident::incidentTypeOptions()),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(DisciplineIncident::statusOptions()),

                Tables\Filters\Filter::make('open')
                    ->label('চলমান ঘটনা')
                    ->query(fn(Builder $query) => $query->open()),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('notifyParent')
                    ->label('অভিভাবক জানান')
                    ->icon('heroicon-o-phone')
                    ->color('warning')
                    ->action(fn($record) => $record->update([
                        'parent_notified' => true,
                        'parent_notified_date' => now(),
                    ]))
                    ->visible(fn($record) => !$record->parent_notified)
                    ->requiresConfirmation(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ])
            ->emptyStateHeading('কোন শৃঙ্খলা ঘটনা নেই')
            ->emptyStateDescription('আলহামদুলিল্লাহ! সব ছাত্র শৃঙ্খলাবদ্ধ')
            ->emptyStateIcon('heroicon-o-shield-check');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDisciplineIncidents::route('/'),
            'create' => Pages\CreateDisciplineIncident::route('/create'),
            'edit' => Pages\EditDisciplineIncident::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::open()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::open()->count();
        return $count > 0 ? 'danger' : 'success';
    }
}
