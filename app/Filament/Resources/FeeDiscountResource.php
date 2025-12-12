<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeDiscountResource\Pages;
use App\Models\FeeDiscount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FeeDiscountResource extends Resource
{
    protected static ?string $model = FeeDiscount::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationLabel = 'ফি ছাড়';

    protected static ?string $modelLabel = 'ফি ছাড়';

    protected static ?string $pluralModelLabel = 'ফি ছাড়সমূহ';

    protected static ?string $navigationGroup = 'ফি ব্যবস্থাপনা';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ছাড়ের তথ্য')
                    ->description('ফি ছাড়ের ধরণ এবং পরিমাণ নির্ধারণ করুন')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('ছাড়ের নাম')
                            ->placeholder('যেমন: এতিম ছাড়, মেধাবী ছাড়')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('discount_type')
                            ->label('ছাড়ের ধরণ')
                            ->options(FeeDiscount::discountTypeOptions())
                            ->required()
                            ->native(false)
                            ->live()
                            ->default('percentage'),

                        Forms\Components\TextInput::make('amount')
                            ->label(fn(Forms\Get $get) => $get('discount_type') === 'percentage' ? 'ছাড়ের শতাংশ (%)' : 'ছাড়ের টাকা (৳)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(fn(Forms\Get $get) => $get('discount_type') === 'percentage' ? 100 : 100000)
                            ->suffix(fn(Forms\Get $get) => $get('discount_type') === 'percentage' ? '%' : '৳')
                            ->placeholder(fn(Forms\Get $get) => $get('discount_type') === 'percentage' ? '0-100' : '0-100000'),

                        Forms\Components\Textarea::make('description')
                            ->label('বিবরণ')
                            ->placeholder('এই ছাড়ের বিস্তারিত বিবরণ লিখুন...')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('সক্রিয়')
                            ->helperText('নিষ্ক্রিয় করলে নতুন ছাত্রদের জন্য এই ছাড় প্রযোজ্য হবে না')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('ছাড়ের নাম')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('discount_type')
                    ->label('ধরণ')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => $state === 'percentage' ? 'শতাংশ' : 'নির্দিষ্ট')
                    ->color(fn(string $state): string => $state === 'percentage' ? 'info' : 'success'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('পরিমাণ')
                    ->formatStateUsing(function (FeeDiscount $record): string {
                        return $record->formatted_discount;
                    })
                    ->sortable()
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('studentFees_count')
                    ->label('ব্যবহৃত')
                    ->counts('studentFees')
                    ->suffix(' জন')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('তৈরি')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('discount_type')
                    ->label('ধরণ')
                    ->options(FeeDiscount::discountTypeOptions()),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('স্ট্যাটাস')
                    ->trueLabel('সক্রিয়')
                    ->falseLabel('নিষ্ক্রিয়'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('কোন ফি ছাড় নেই')
            ->emptyStateDescription('নতুন ফি ছাড় যোগ করতে উপরের বাটনে ক্লিক করুন')
            ->emptyStateIcon('heroicon-o-receipt-percent');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeDiscounts::route('/'),
            'create' => Pages\CreateFeeDiscount::route('/create'),
            'edit' => Pages\EditFeeDiscount::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
