<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicYearResource\Pages;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class AcademicYearResource extends BaseResource
{
    protected static ?string $model = AcademicYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'একাডেমিক সেটআপ';

    protected static ?string $modelLabel = 'শিক্ষাবর্ষ';

    protected static ?string $pluralModelLabel = 'শিক্ষাবর্ষসমূহ';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('শিক্ষাবর্ষের নাম')
                    ->placeholder('যেমন: ২০২৪-২০২৫')
                    ->required()
                    ->maxLength(50),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('শুরুর তারিখ')
                            ->required(),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('শেষের তারিখ')
                            ->required()
                            ->after('start_date'),
                    ]),

                Forms\Components\Toggle::make('is_current')
                    ->label('বর্তমান শিক্ষাবর্ষ')
                    ->helperText('এটি চালু করলে আগের বর্তমান শিক্ষাবর্ষ স্বয়ংক্রিয়ভাবে বন্ধ হয়ে যাবে'),

                Forms\Components\Select::make('status')
                    ->label('স্ট্যাটাস')
                    ->options([
                        'active' => 'সক্রিয়',
                        'inactive' => 'নিষ্ক্রিয়',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('শিক্ষাবর্ষ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('শুরু')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('শেষ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_current')
                    ->label('বর্তমান')
                    ->boolean(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->formatStateUsing(fn(string $state): string => $state === 'active' ? 'সক্রিয়' : 'নিষ্ক্রিয়'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('set_current')
                    ->label('বর্তমান করুন')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn(AcademicYear $record) => $record->setAsCurrent())
                    ->requiresConfirmation()
                    ->visible(fn(AcademicYear $record): bool => !$record->is_current),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_date', 'desc');
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
            'index' => Pages\ListAcademicYears::route('/'),
            'create' => Pages\CreateAcademicYear::route('/create'),
            'edit' => Pages\EditAcademicYear::route('/{record}/edit'),
        ];
    }
}
