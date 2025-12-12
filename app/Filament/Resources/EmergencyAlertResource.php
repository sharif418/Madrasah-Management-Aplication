<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmergencyAlertResource\Pages;
use App\Models\EmergencyAlert;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class EmergencyAlertResource extends Resource
{
    protected static ?string $model = EmergencyAlert::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'যোগাযোগ';

    protected static ?string $navigationLabel = 'জরুরি বার্তা';

    protected static ?string $modelLabel = 'জরুরি বার্তা';

    protected static ?string $pluralModelLabel = 'জরুরি বার্তা';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('জরুরি বার্তা')
                    ->description('জরুরি পরিস্থিতিতে বার্তা পাঠান')
                    ->icon('heroicon-o-bell-alert')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('শিরোনাম')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('message')
                            ->label('বার্তা')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('severity')
                                    ->label('তীব্রতা')
                                    ->options(EmergencyAlert::getSeverityOptions())
                                    ->default('warning')
                                    ->native(false),

                                Forms\Components\Select::make('target')
                                    ->label('প্রাপক')
                                    ->options(EmergencyAlert::getTargetOptions())
                                    ->default('all')
                                    ->native(false),

                                Forms\Components\DateTimePicker::make('expires_at')
                                    ->label('মেয়াদ শেষ')
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('send_sms')
                                    ->label('SMS পাঠাও')
                                    ->helperText('SMS Gateway কনফিগার থাকলে কাজ করবে'),

                                Forms\Components\Toggle::make('send_email')
                                    ->label('Email পাঠাও'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('সক্রিয়')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('শিরোনাম')
                    ->searchable()
                    ->weight('bold')
                    ->limit(40),

                Tables\Columns\TextColumn::make('severity')
                    ->label('তীব্রতা')
                    ->badge()
                    ->formatStateUsing(fn($state) => EmergencyAlert::getSeverityOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'critical' => 'danger',
                        'warning' => 'warning',
                        default => 'info',
                    }),

                Tables\Columns\TextColumn::make('target')
                    ->label('প্রাপক')
                    ->formatStateUsing(fn($state) => EmergencyAlert::getTargetOptions()[$state] ?? $state)
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('send_sms')
                    ->label('SMS')
                    ->boolean(),

                Tables\Columns\IconColumn::make('send_email')
                    ->label('Email')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('মেয়াদ')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('সীমাহীন'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('তৈরি')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('severity')
                    ->label('তীব্রতা')
                    ->options(EmergencyAlert::getSeverityOptions()),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('সক্রিয়'),
            ])
            ->actions([
                Tables\Actions\Action::make('deactivate')
                    ->label('নিষ্ক্রিয়')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->is_active)
                    ->requiresConfirmation()
                    ->action(function (EmergencyAlert $record) {
                        $record->update(['is_active' => false]);
                        Notification::make()->success()->title('বার্তা নিষ্ক্রিয় করা হয়েছে')->send();
                    }),

                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmergencyAlerts::route('/'),
            'create' => Pages\CreateEmergencyAlert::route('/create'),
            'edit' => Pages\EditEmergencyAlert::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::active()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
