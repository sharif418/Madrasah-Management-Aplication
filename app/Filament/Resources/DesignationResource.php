<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DesignationResource\Pages;
use App\Models\Designation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DesignationResource extends Resource
{
    protected static ?string $model = Designation::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'একাডেমিক সেটআপ';

    protected static ?string $modelLabel = 'পদবী';

    protected static ?string $pluralModelLabel = 'পদবীসমূহ';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('পদবীর তথ্য')
                    ->description('শিক্ষক/কর্মচারীদের পদবী সংক্রান্ত তথ্য')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('পদবীর নাম (বাংলায়)')
                                    ->placeholder('যেমন: মুহতামিম, উস্তাদ, হাফেজ সাহেব')
                                    ->required()
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true),

                                Forms\Components\TextInput::make('name_en')
                                    ->label('পদবীর নাম (ইংরেজিতে)')
                                    ->placeholder('e.g., Principal, Teacher')
                                    ->maxLength(100),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->label('ক্রম/র‍্যাঙ্ক')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('ছোট সংখ্যা = উচ্চ পদবী'),

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
                Tables\Columns\TextColumn::make('order')
                    ->label('র‍্যাঙ্ক')
                    ->sortable()
                    ->alignCenter()
                    ->width(60),

                Tables\Columns\TextColumn::make('name')
                    ->label('পদবী (বাংলা)')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name_en')
                    ->label('পদবী (English)')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('teachers_count')
                    ->label('শিক্ষক সংখ্যা')
                    ->counts('teachers')
                    ->badge()
                    ->color('success')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('staff_count')
                    ->label('কর্মচারী সংখ্যা')
                    ->counts('staff')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('স্ট্যাটাস'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->emptyStateHeading('কোন পদবী নেই')
            ->emptyStateDescription('নতুন পদবী যোগ করুন')
            ->emptyStateIcon('heroicon-o-identification');
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
            'index' => Pages\ListDesignations::route('/'),
            'create' => Pages\CreateDesignation::route('/create'),
            'edit' => Pages\EditDesignation::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }
}
