<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseHeadResource\Pages;
use App\Models\ExpenseHead;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ExpenseHeadResource extends BaseResource
{
    protected static ?string $model = ExpenseHead::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';

    protected static ?string $navigationGroup = 'হিসাব ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'ব্যয়ের খাত';

    protected static ?string $pluralModelLabel = 'ব্যয়ের খাতসমূহ';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ব্যয়ের খাত')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('নাম')
                            ->placeholder('যেমন: বেতন, বিদ্যুৎ বিল, রক্ষণাবেক্ষণ')
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

                Tables\Columns\TextColumn::make('expenses_count')
                    ->label('এন্ট্রি সংখ্যা')
                    ->counts('expenses')
                    ->badge()
                    ->color('danger'),

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
            ->emptyStateHeading('কোন ব্যয়ের খাত নেই')
            ->emptyStateIcon('heroicon-o-arrow-trending-down');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenseHeads::route('/'),
            'create' => Pages\CreateExpenseHead::route('/create'),
            'edit' => Pages\EditExpenseHead::route('/{record}/edit'),
        ];
    }
}
