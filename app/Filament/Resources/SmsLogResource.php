<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SmsLogResource\Pages;
use App\Models\SmsLog;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SmsLogResource extends BaseResource
{
    protected static ?string $model = SmsLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'যোগাযোগ';

    protected static ?string $modelLabel = 'SMS';

    protected static ?string $pluralModelLabel = 'SMS লগ';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('SMS পাঠান')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('মোবাইল নম্বর')
                            ->tel()
                            ->required()
                            ->prefix('+880')
                            ->placeholder('1XXXXXXXXX'),

                        Forms\Components\Textarea::make('message')
                            ->label('মেসেজ')
                            ->required()
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('সর্বোচ্চ ১৬০ অক্ষর'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('ধরণ')
                                    ->options(SmsLog::typeOptions())
                                    ->default('single')
                                    ->native(false),

                                Forms\Components\Select::make('purpose')
                                    ->label('উদ্দেশ্য')
                                    ->options(SmsLog::purposeOptions())
                                    ->default('other')
                                    ->native(false),
                            ]),

                        Forms\Components\Hidden::make('status')
                            ->default('pending'),

                        Forms\Components\Hidden::make('sent_by')
                            ->default(fn() => Auth::id()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phone')
                    ->label('মোবাইল')
                    ->searchable()
                    ->prefix('+880'),

                Tables\Columns\TextColumn::make('message')
                    ->label('মেসেজ')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->message),

                Tables\Columns\TextColumn::make('purpose')
                    ->label('উদ্দেশ্য')
                    ->badge()
                    ->formatStateUsing(fn($state) => SmsLog::purposeOptions()[$state] ?? $state)
                    ->color('info'),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => SmsLog::statusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('sentBy.name')
                    ->label('প্রেরক')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('সময়')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('purpose')
                    ->label('উদ্দেশ্য')
                    ->options(SmsLog::purposeOptions()),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(SmsLog::statusOptions()),
            ])
            ->actions([
                Tables\Actions\Action::make('resend')
                    ->label('আবার পাঠান')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn(SmsLog $record): bool => $record->status === 'failed'),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('কোন SMS নেই')
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSmsLogs::route('/'),
            'create' => Pages\CreateSmsLog::route('/create'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', today())->count() ?: null;
    }
}
