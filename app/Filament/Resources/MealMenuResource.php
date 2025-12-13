<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MealMenuResource\Pages;
use App\Models\MealMenu;
use App\Models\Hostel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MealMenuResource extends Resource
{
    protected static ?string $model = MealMenu::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';

    protected static ?string $navigationGroup = 'হোস্টেল ও পরিবহন';

    protected static ?string $navigationLabel = 'খাবার মেনু';

    protected static ?string $modelLabel = 'খাবার মেনু';

    protected static ?string $pluralModelLabel = 'খাবার মেনু';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('মেনু তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('hostel_id')
                                    ->label('হোস্টেল')
                                    ->options(Hostel::where('is_active', true)->pluck('name', 'id'))
                                    ->placeholder('সকল হোস্টেল')
                                    ->native(false),

                                Forms\Components\Select::make('day_of_week')
                                    ->label('বার')
                                    ->options(MealMenu::dayOptions())
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('meal_type')
                                    ->label('খাবারের ধরণ')
                                    ->options(MealMenu::mealTypeOptions())
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\TagsInput::make('menu_items')
                            ->label('খাবারের তালিকা')
                            ->placeholder('যেমন: ভাত, মাছ, সবজি')
                            ->helperText('প্রতিটি আইটেম লিখে Enter চাপুন')
                            ->required(),

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
                Tables\Columns\TextColumn::make('hostel.name')
                    ->label('হোস্টেল')
                    ->default('সকল')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('বার')
                    ->formatStateUsing(fn($state) => MealMenu::dayOptions()[$state] ?? $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('meal_type')
                    ->label('ধরণ')
                    ->formatStateUsing(fn($state) => MealMenu::mealTypeOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'breakfast' => 'warning',
                        'lunch' => 'success',
                        'snacks' => 'info',
                        'dinner' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('menu_items')
                    ->label('খাবার')
                    ->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean(),
            ])
            ->defaultSort('day_of_week')
            ->filters([
                Tables\Filters\SelectFilter::make('day_of_week')
                    ->label('বার')
                    ->options(MealMenu::dayOptions()),

                Tables\Filters\SelectFilter::make('meal_type')
                    ->label('ধরণ')
                    ->options(MealMenu::mealTypeOptions()),

                Tables\Filters\SelectFilter::make('hostel_id')
                    ->label('হোস্টেল')
                    ->options(Hostel::pluck('name', 'id')),
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
            ->emptyStateHeading('কোন মেনু নেই')
            ->emptyStateIcon('heroicon-o-cake');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMealMenus::route('/'),
            'create' => Pages\CreateMealMenu::route('/create'),
            'edit' => Pages\EditMealMenu::route('/{record}/edit'),
        ];
    }
}
