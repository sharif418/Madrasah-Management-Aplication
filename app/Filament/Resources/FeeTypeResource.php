<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeTypeResource\Pages;
use App\Models\FeeType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class FeeTypeResource extends Resource
{
    protected static ?string $model = FeeType::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'ফি ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'ফি এর ধরণ';

    protected static ?string $pluralModelLabel = 'ফি এর ধরণসমূহ';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ফি এর ধরণ')
                    ->description('বিভিন্ন ধরনের ফি সেটআপ করুন')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('নাম')
                                    ->placeholder('যেমন: ভর্তি ফি, মাসিক বেতন')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn($state, Forms\Set $set) =>
                                        $set('code', Str::upper(Str::slug($state, '_')))
                                    )
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('code')
                                    ->label('কোড')
                                    ->placeholder('ADMISSION_FEE')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('বিবরণ')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_recurring')
                                    ->label('পুনরাবৃত্ত ফি')
                                    ->helperText('মাসিক/বার্ষিক ইত্যাদি')
                                    ->default(false),

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
                Tables\Columns\TextColumn::make('name')
                    ->label('নাম')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('code')
                    ->label('কোড')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_recurring')
                    ->label('পুনরাবৃত্ত')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('fee_structures_count')
                    ->label('কাঠামো সংখ্যা')
                    ->counts('feeStructures')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_recurring')
                    ->label('পুনরাবৃত্ত'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('স্ট্যাটাস'),
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
            ->defaultSort('name')
            ->striped()
            ->emptyStateHeading('কোন ফি এর ধরণ নেই')
            ->emptyStateDescription('নতুন ফি এর ধরণ যোগ করুন')
            ->emptyStateIcon('heroicon-o-banknotes');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeTypes::route('/'),
            'create' => Pages\CreateFeeType::route('/create'),
            'edit' => Pages\EditFeeType::route('/{record}/edit'),
        ];
    }
}
