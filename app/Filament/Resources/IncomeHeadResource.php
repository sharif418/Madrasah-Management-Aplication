<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncomeHeadResource\Pages;
use App\Models\IncomeHead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class IncomeHeadResource extends Resource
{
    protected static ?string $model = IncomeHead::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'আয়ের খাত';

    protected static ?string $pluralModelLabel = 'আয়ের খাতসমূহ';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('আয়ের খাত')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('নাম')
                            ->placeholder('যেমন: ছাত্র বেতন, দান-অনুদান')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn($state, Forms\Set $set) =>
                                $set('code', Str::upper(Str::slug($state, '_')))
                            )
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label('কোড')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\Textarea::make('description')
                            ->label('বিবরণ')
                            ->rows(2),

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
                    ->label('নাম')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('code')
                    ->label('কোড')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('incomes_count')
                    ->label('এন্ট্রি সংখ্যা')
                    ->counts('incomes')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('কোন আয়ের খাত নেই')
            ->emptyStateIcon('heroicon-o-arrow-trending-up');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncomeHeads::route('/'),
            'create' => Pages\CreateIncomeHead::route('/create'),
            'edit' => Pages\EditIncomeHead::route('/{record}/edit'),
        ];
    }
}
