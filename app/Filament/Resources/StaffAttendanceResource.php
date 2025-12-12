<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffAttendanceResource\Pages;
use App\Models\StaffAttendance;
use App\Models\Teacher;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class StaffAttendanceResource extends Resource
{
    protected static ?string $model = StaffAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'উপস্থিতি ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'স্টাফ হাজিরা';

    protected static ?string $pluralModelLabel = 'স্টাফ হাজিরা';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('হাজিরা তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('attendee_type')
                                    ->label('ধরণ')
                                    ->options(StaffAttendance::attendeeTypeOptions())
                                    ->required()
                                    ->live()
                                    ->native(false)
                                    ->afterStateUpdated(fn($set) => $set('attendee_id', null)),

                                Forms\Components\Select::make('attendee_id')
                                    ->label('নাম')
                                    ->options(function (Get $get) {
                                        if ($get('attendee_type') === 'teacher') {
                                            return Teacher::where('status', 'active')->pluck('name', 'id');
                                        }
                                        return Staff::where('status', 'active')->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->native(false)
                                    ->searchable(),

                                Forms\Components\DatePicker::make('date')
                                    ->label('তারিখ')
                                    ->default(now())
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(StaffAttendance::statusOptions())
                                    ->default('present')
                                    ->required()
                                    ->native(false),

                                Forms\Components\TimePicker::make('check_in')
                                    ->label('চেক ইন')
                                    ->native(false),

                                Forms\Components\TimePicker::make('check_out')
                                    ->label('চেক আউট')
                                    ->native(false),
                            ]),

                        Forms\Components\Textarea::make('remarks')
                            ->label('মন্তব্য')
                            ->rows(2),

                        Forms\Components\Hidden::make('marked_by')
                            ->default(fn() => Auth::id()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attendee_type')
                    ->label('ধরণ')
                    ->badge()
                    ->formatStateUsing(fn($state) => StaffAttendance::attendeeTypeOptions()[$state] ?? $state)
                    ->color(fn($state) => $state === 'teacher' ? 'info' : 'warning'),

                Tables\Columns\TextColumn::make('attendee.name')
                    ->label('নাম')
                    ->weight('bold')
                    ->getStateUsing(fn($record) => $record->attendee?->name ?? 'N/A'),

                Tables\Columns\TextColumn::make('date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => StaffAttendance::statusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'half_day' => 'info',
                        'leave' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('check_in')
                    ->label('চেক ইন')
                    ->time('h:i A')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('check_out')
                    ->label('চেক আউট')
                    ->time('h:i A')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('attendee_type')
                    ->label('ধরণ')
                    ->options(StaffAttendance::attendeeTypeOptions()),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(StaffAttendance::statusOptions()),

                Tables\Filters\Filter::make('today')
                    ->label('আজকে')
                    ->query(fn($query) => $query->whereDate('date', today()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('date', 'desc')
            ->emptyStateHeading('কোন হাজিরা নেই')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaffAttendances::route('/'),
            'create' => Pages\CreateStaffAttendance::route('/create'),
            'bulk' => Pages\BulkStaffAttendance::route('/bulk'),
            'edit' => Pages\EditStaffAttendance::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $today = StaffAttendance::whereDate('date', today())->count();
        return $today ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
}
