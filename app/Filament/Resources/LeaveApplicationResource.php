<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveApplicationResource\Pages;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaveApplicationResource extends Resource
{
    protected static ?string $model = LeaveApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'উপস্থিতি ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'ছুটির আবেদন';

    protected static ?string $pluralModelLabel = 'ছুটির আবেদনসমূহ';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ছুটির আবেদন')
                    ->description('ছুটির বিস্তারিত তথ্য পূরণ করুন')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('applicant_type')
                                    ->label('আবেদনকারীর ধরণ')
                                    ->options([
                                        'student' => 'ছাত্র',
                                        'teacher' => 'শিক্ষক',
                                        'staff' => 'কর্মচারী',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                Forms\Components\Select::make('applicant_id')
                                    ->label('আবেদনকারী')
                                    ->options(function (Forms\Get $get) {
                                        $type = $get('applicant_type');
                                        if (!$type)
                                            return [];

                                        return match ($type) {
                                            'student' => \App\Models\Student::where('status', 'active')->pluck('name', 'id'),
                                            'teacher' => \App\Models\Teacher::where('status', 'active')->pluck('name', 'id'),
                                            'staff' => \App\Models\Staff::where('status', 'active')->pluck('name', 'id'),
                                            default => [],
                                        };
                                    })
                                    ->searchable()
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('leave_type_id')
                                    ->label('ছুটির ধরণ')
                                    ->relationship('leaveType', 'name', fn(Builder $query) => $query->where('is_active', true))
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('নাম')
                                            ->required(),
                                        Forms\Components\TextInput::make('days_allowed')
                                            ->label('অনুমোদিত দিন')
                                            ->numeric()
                                            ->default(0),
                                    ])
                                    ->createOptionModalHeading('নতুন ছুটির ধরণ'),

                                Forms\Components\DatePicker::make('start_date')
                                    ->label('শুরুর তারিখ')
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('শেষ তারিখ')
                                    ->required()
                                    ->native(false)
                                    ->afterOrEqual('start_date'),
                            ]),

                        Forms\Components\Textarea::make('reason')
                            ->label('ছুটির কারণ')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->label('স্ট্যাটাস')
                            ->options(LeaveApplication::statusOptions())
                            ->default('pending')
                            ->required()
                            ->native(false)
                            ->visible(fn() => auth()->user()?->hasRole(['super_admin', 'principal', 'academic_admin'])),

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('প্রত্যাখ্যানের কারণ')
                            ->rows(2)
                            ->visible(fn(Forms\Get $get) => $get('status') === 'rejected')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('applicant_type')
                    ->label('ধরণ')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'student' => 'ছাত্র',
                        'teacher' => 'শিক্ষক',
                        'staff' => 'কর্মচারী',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'student' => 'info',
                        'teacher' => 'success',
                        'staff' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('applicant_id')
                    ->label('আবেদনকারী')
                    ->formatStateUsing(function ($record) {
                        return match ($record->applicant_type) {
                            'student' => \App\Models\Student::find($record->applicant_id)?->name,
                            'teacher' => \App\Models\Teacher::find($record->applicant_id)?->name,
                            'staff' => \App\Models\Staff::find($record->applicant_id)?->name,
                            default => '-',
                        };
                    })
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('leaveType.name')
                    ->label('ছুটির ধরণ')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('শুরু')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('শেষ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('days_count')
                    ->label('দিন')
                    ->state(fn(LeaveApplication $record): int => $record->days_count)
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => LeaveApplication::statusOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('আবেদনের তারিখ')
                    ->dateTime('d M Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(LeaveApplication::statusOptions()),

                Tables\Filters\SelectFilter::make('applicant_type')
                    ->label('আবেদনকারীর ধরণ')
                    ->options([
                        'student' => 'ছাত্র',
                        'teacher' => 'শিক্ষক',
                        'staff' => 'কর্মচারী',
                    ]),

                Tables\Filters\SelectFilter::make('leave_type_id')
                    ->label('ছুটির ধরণ')
                    ->relationship('leaveType', 'name')
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('অনুমোদন')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (LeaveApplication $record): void {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    })
                    ->visible(fn(LeaveApplication $record): bool => $record->status === 'pending'),

                Tables\Actions\Action::make('reject')
                    ->label('প্রত্যাখ্যান')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('প্রত্যাখ্যানের কারণ')
                            ->required()
                            ->rows(2),
                    ])
                    ->action(function (LeaveApplication $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                    })
                    ->visible(fn(LeaveApplication $record): bool => $record->status === 'pending'),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('কোন ছুটির আবেদন নেই')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveApplications::route('/'),
            'create' => Pages\CreateLeaveApplication::route('/create'),
            'edit' => Pages\EditLeaveApplication::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
