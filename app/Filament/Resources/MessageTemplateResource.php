<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageTemplateResource\Pages;
use App\Models\MessageTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MessageTemplateResource extends Resource
{
    protected static ?string $model = MessageTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'যোগাযোগ';

    protected static ?string $navigationLabel = 'মেসেজ টেমপ্লেট';

    protected static ?string $modelLabel = 'টেমপ্লেট';

    protected static ?string $pluralModelLabel = 'মেসেজ টেমপ্লেট';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('টেমপ্লেট তথ্য')
                    ->description('SMS/Email মেসেজ টেমপ্লেট তৈরি করুন')
                    ->icon('heroicon-o-document-duplicate')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('টেমপ্লেট নাম')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Select::make('type')
                                    ->label('ধরণ')
                                    ->options(MessageTemplate::getTypeOptions())
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                Forms\Components\Select::make('category')
                                    ->label('ক্যাটাগরি')
                                    ->options(MessageTemplate::getCategoryOptions())
                                    ->native(false),
                            ]),

                        Forms\Components\TextInput::make('subject')
                            ->label('ইমেইল সাবজেক্ট')
                            ->visible(fn(Forms\Get $get) => in_array($get('type'), ['email', 'both']))
                            ->placeholder('অভিভাবক আপনার সন্তানের...')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('content')
                            ->label('মেসেজ কন্টেন্ট')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('প্লেসহোল্ডার: {student_name}, {class}, {amount}, {month}, {institution}, {phone}'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('সক্রিয়')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('টেমপ্লেট নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->label('ধরণ')
                    ->badge()
                    ->formatStateUsing(fn($state) => MessageTemplate::getTypeOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'sms' => 'primary',
                        'email' => 'success',
                        'both' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('category')
                    ->label('ক্যাটাগরি')
                    ->formatStateUsing(fn($state) => MessageTemplate::getCategoryOptions()[$state] ?? $state)
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('content')
                    ->label('কন্টেন্ট')
                    ->limit(50),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('ধরণ')
                    ->options(MessageTemplate::getTypeOptions()),

                Tables\Filters\SelectFilter::make('category')
                    ->label('ক্যাটাগরি')
                    ->options(MessageTemplate::getCategoryOptions()),
            ])
            ->actions([
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
            'index' => Pages\ListMessageTemplates::route('/'),
            'create' => Pages\CreateMessageTemplate::route('/create'),
            'edit' => Pages\EditMessageTemplate::route('/{record}/edit'),
        ];
    }
}
