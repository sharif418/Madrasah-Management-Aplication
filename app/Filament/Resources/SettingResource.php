<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends BaseResource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'সেটিংস';

    protected static ?string $modelLabel = 'সেটিং';

    protected static ?string $pluralModelLabel = 'সেটিংস';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('সেটিং')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('group')
                                    ->label('গ্রুপ')
                                    ->options(Setting::groupOptions())
                                    ->default('general')
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('key')
                                    ->label('কী')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('site_name, sms_api_key'),
                            ]),

                        Forms\Components\Select::make('type')
                            ->label('টাইপ')
                            ->options(Setting::typeOptions())
                            ->default('text')
                            ->native(false)
                            ->live(),

                        Forms\Components\TextInput::make('value')
                            ->label('মান')
                            ->visible(fn($get) => in_array($get('type'), ['text', 'number']))
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('value')
                            ->label('মান')
                            ->rows(3)
                            ->visible(fn($get) => $get('type') === 'textarea')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('value')
                            ->label('সক্রিয়')
                            ->visible(fn($get) => $get('type') === 'boolean'),

                        Forms\Components\Textarea::make('description')
                            ->label('বিবরণ')
                            ->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->label('গ্রুপ')
                    ->badge()
                    ->formatStateUsing(fn($state) => Setting::groupOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'general' => 'info',
                        'academic' => 'success',
                        'sms' => 'warning',
                        'payment' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('key')
                    ->label('কী')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('value')
                    ->label('মান')
                    ->limit(30),

                Tables\Columns\TextColumn::make('type')
                    ->label('টাইপ')
                    ->formatStateUsing(fn($state) => Setting::typeOptions()[$state] ?? $state),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('গ্রুপ')
                    ->options(Setting::groupOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateHeading('কোন সেটিং নেই')
            ->emptyStateIcon('heroicon-o-cog-6-tooth');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
