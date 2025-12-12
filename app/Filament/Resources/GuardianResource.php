<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuardianResource\Pages;
use App\Models\Guardian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\ActionGroup;
use Filament\Support\Enums\FontWeight;

class GuardianResource extends Resource
{
    protected static ?string $model = Guardian::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'ছাত্র ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'অভিভাবক';

    protected static ?string $pluralModelLabel = 'অভিভাবকগণ';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('অভিভাবকের তথ্য')
                    ->description('অভিভাবক সম্পর্কিত বিস্তারিত তথ্য')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->label('ছবি')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('guardians/photos')
                                    ->avatar()
                                    ->circleCropper()
                                    ->columnSpan(1),

                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('নাম')
                                            ->placeholder('অভিভাবকের সম্পূর্ণ নাম')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('phone')
                                                    ->label('মোবাইল নম্বর')
                                                    ->tel()
                                                    ->prefix('+880')
                                                    ->required()
                                                    ->maxLength(15),

                                                Forms\Components\TextInput::make('email')
                                                    ->label('ইমেইল')
                                                    ->email()
                                                    ->maxLength(255),
                                            ]),
                                    ])
                                    ->columnSpan(2),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('relation')
                                    ->label('সম্পর্ক')
                                    ->options([
                                        'পিতা' => 'পিতা',
                                        'মাতা' => 'মাতা',
                                        'ভাই' => 'ভাই',
                                        'বোন' => 'বোন',
                                        'চাচা' => 'চাচা',
                                        'মামা' => 'মামা',
                                        'ফুফু' => 'ফুফু',
                                        'খালা' => 'খালা',
                                        'দাদা' => 'দাদা',
                                        'দাদি' => 'দাদি',
                                        'নানা' => 'নানা',
                                        'নানি' => 'নানি',
                                        'অন্যান্য' => 'অন্যান্য',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->searchable(),

                                Forms\Components\TextInput::make('occupation')
                                    ->label('পেশা')
                                    ->placeholder('যেমন: ব্যবসায়ী, চাকরিজীবী')
                                    ->maxLength(100),

                                Forms\Components\TextInput::make('nid')
                                    ->label('জাতীয় পরিচয়পত্র নম্বর')
                                    ->maxLength(20),
                            ]),

                        Forms\Components\Textarea::make('address')
                            ->label('ঠিকানা')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->label('স্ট্যাটাস')
                            ->options([
                                'active' => 'সক্রিয়',
                                'inactive' => 'নিষ্ক্রিয়',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(fn() => asset('images/default-avatar.png')),

                Tables\Columns\TextColumn::make('name')
                    ->label('নাম')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('relation')
                    ->label('সম্পর্ক')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('মোবাইল')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),

                Tables\Columns\TextColumn::make('occupation')
                    ->label('পেশা')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('ছাত্র সংখ্যা')
                    ->counts('students')
                    ->badge()
                    ->color('success')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'active' ? 'success' : 'gray')
                    ->formatStateUsing(fn(string $state): string => $state === 'active' ? 'সক্রিয়' : 'নিষ্ক্রিয়')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('যোগ করার তারিখ')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('relation')
                    ->label('সম্পর্ক')
                    ->options([
                        'পিতা' => 'পিতা',
                        'মাতা' => 'মাতা',
                        'ভাই' => 'ভাই',
                        'চাচা' => 'চাচা',
                        'মামা' => 'মামা',
                        'অন্যান্য' => 'অন্যান্য',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options([
                        'active' => 'সক্রিয়',
                        'inactive' => 'নিষ্ক্রিয়',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name')
            ->emptyStateHeading('কোন অভিভাবক নেই')
            ->emptyStateDescription('নতুন অভিভাবক যোগ করতে নিচের বাটনে ক্লিক করুন')
            ->emptyStateIcon('heroicon-o-users');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('অভিভাবকের তথ্য')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\ImageEntry::make('photo')
                                    ->label('')
                                    ->circular()
                                    ->size(80),

                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('name')
                                        ->label('নাম')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold),
                                    Infolists\Components\TextEntry::make('relation')
                                        ->label('সম্পর্ক')
                                        ->badge()
                                        ->color('info'),
                                ])
                                    ->columnSpan(2),
                            ]),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('phone')
                                    ->label('মোবাইল')
                                    ->icon('heroicon-o-phone'),
                                Infolists\Components\TextEntry::make('email')
                                    ->label('ইমেইল')
                                    ->icon('heroicon-o-envelope'),
                                Infolists\Components\TextEntry::make('occupation')
                                    ->label('পেশা'),
                                Infolists\Components\TextEntry::make('nid')
                                    ->label('NID'),
                                Infolists\Components\TextEntry::make('address')
                                    ->label('ঠিকানা')
                                    ->columnSpan(2),
                            ]),
                    ]),

                Infolists\Components\Section::make('সন্তান/পোষ্যদের তালিকা')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('students')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('নাম'),
                                Infolists\Components\TextEntry::make('admission_no')
                                    ->label('ভর্তি নং'),
                                Infolists\Components\TextEntry::make('class.name')
                                    ->label('শ্রেণি')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->badge()
                                    ->color(fn(string $state): string => $state === 'active' ? 'success' : 'gray'),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuardians::route('/'),
            'create' => Pages\CreateGuardian::route('/create'),
            'view' => Pages\ViewGuardian::route('/{record}'),
            'edit' => Pages\EditGuardian::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone', 'nid'];
    }
}
