<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HostelAllocationResource\Pages;
use App\Models\HostelAllocation;
use App\Models\HostelRoom;
use App\Models\Hostel;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class HostelAllocationResource extends BaseResource
{
    protected static ?string $model = HostelAllocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationGroup = 'হোস্টেল ও পরিবহন';

    protected static ?string $modelLabel = 'সিট বরাদ্দ';

    protected static ?string $pluralModelLabel = 'সিট বরাদ্দসমূহ';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('সিট বরাদ্দ')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('ছাত্র')
                            ->relationship('student', 'name', fn(Builder $query) => $query->where('status', 'active'))
                            ->required()
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name_bn} ({$record->student_id})"),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('hostel_id')
                                    ->label('হোস্টেল')
                                    ->relationship('hostel', 'name', fn(Builder $query) => $query->active())
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn(Forms\Set $set) => $set('hostel_room_id', null)),

                                Forms\Components\Select::make('hostel_room_id')
                                    ->label('রুম')
                                    ->options(function (Forms\Get $get) {
                                        $hostelId = $get('hostel_id');
                                        if (!$hostelId)
                                            return [];

                                        return HostelRoom::where('hostel_id', $hostelId)
                                            ->where('status', '!=', 'maintenance')
                                            ->get()
                                            ->filter(fn($room) => $room->hasSpace())
                                            ->mapWithKeys(fn($room) => [
                                                $room->id => "রুম {$room->room_no} (খালি: {$room->available_beds})"
                                            ]);
                                    })
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('bed_no')
                                    ->label('বেড নং'),

                                Forms\Components\DatePicker::make('allocation_date')
                                    ->label('বরাদ্দের তারিখ')
                                    ->default(now())
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(HostelAllocation::statusOptions())
                                    ->default('active')
                                    ->native(false)
                                    ->disabled(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('ছাত্র')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn($record) => $record->student?->student_id),

                Tables\Columns\TextColumn::make('hostel.name')
                    ->label('হোস্টেল')
                    ->sortable(),

                Tables\Columns\TextColumn::make('hostelRoom.room_no')
                    ->label('রুম')
                    ->badge()
                    ->color('primary')
                    ->prefix('রুম '),

                Tables\Columns\TextColumn::make('bed_no')
                    ->label('বেড')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('allocation_date')
                    ->label('বরাদ্দ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('vacate_date')
                    ->label('ছেড়ে দিয়েছে')
                    ->date('d M Y')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => HostelAllocation::statusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'active' => 'success',
                        'vacated' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hostel_id')
                    ->label('হোস্টেল')
                    ->relationship('hostel', 'name')
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(HostelAllocation::statusOptions()),
            ])
            ->actions([
                Tables\Actions\Action::make('vacate')
                    ->label('ছেড়ে দিন')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('সিট ছেড়ে দেওয়া নিশ্চিত করুন')
                    ->action(function (HostelAllocation $record): void {
                        $record->vacate();

                        Notification::make()
                            ->success()
                            ->title('সিট ছেড়ে দেওয়া হয়েছে')
                            ->send();
                    })
                    ->visible(fn(HostelAllocation $record): bool => $record->status === 'active'),

                Tables\Actions\EditAction::make()
                    ->visible(fn(HostelAllocation $record): bool => $record->status === 'active'),
            ])
            ->defaultSort('allocation_date', 'desc')
            ->emptyStateHeading('কোন বরাদ্দ নেই')
            ->emptyStateIcon('heroicon-o-user-plus');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHostelAllocations::route('/'),
            'create' => Pages\CreateHostelAllocation::route('/create'),
            'edit' => Pages\EditHostelAllocation::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count() ?: null;
    }
}
