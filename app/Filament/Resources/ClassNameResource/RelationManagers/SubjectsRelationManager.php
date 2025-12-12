<?php

namespace App\Filament\Resources\ClassNameResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'subjects';

    protected static ?string $title = 'বিষয়সমূহ';

    protected static ?string $modelLabel = 'বিষয়';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('recordId')
                    ->label('বিষয়')
                    ->relationship('subjects', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('full_marks')
                    ->label('পূর্ণ নম্বর')
                    ->numeric()
                    ->default(100)
                    ->required(),

                Forms\Components\TextInput::make('pass_marks')
                    ->label('পাস নম্বর')
                    ->numeric()
                    ->default(33)
                    ->required(),

                Forms\Components\Toggle::make('is_optional')
                    ->label('ঐচ্ছিক বিষয়')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('বিষয়ের নাম')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('code')
                    ->label('কোড')
                    ->badge(),

                Tables\Columns\TextColumn::make('pivot.full_marks')
                    ->label('পূর্ণ নম্বর')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('pivot.pass_marks')
                    ->label('পাস নম্বর')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('pivot.is_optional')
                    ->label('ঐচ্ছিক')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('type')
                    ->label('ধরণ')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'theory' => 'info',
                        'practical' => 'warning',
                        'religious' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'theory' => 'তত্ত্বীয়',
                        'practical' => 'ব্যবহারিক',
                        'religious' => 'দ্বীনী',
                        default => $state,
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('বিষয় যোগ করুন')
                    ->preloadRecordSelect()
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('full_marks')
                            ->label('পূর্ণ নম্বর')
                            ->numeric()
                            ->default(100)
                            ->required(),
                        Forms\Components\TextInput::make('pass_marks')
                            ->label('পাস নম্বর')
                            ->numeric()
                            ->default(33)
                            ->required(),
                        Forms\Components\Toggle::make('is_optional')
                            ->label('ঐচ্ছিক')
                            ->default(false),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\TextInput::make('full_marks')
                            ->label('পূর্ণ নম্বর')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('pass_marks')
                            ->label('পাস নম্বর')
                            ->numeric()
                            ->required(),
                        Forms\Components\Toggle::make('is_optional')
                            ->label('ঐচ্ছিক'),
                    ]),
                Tables\Actions\DetachAction::make()
                    ->label('সরিয়ে ফেলুন'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('বিষয় নেই')
            ->emptyStateDescription('এই শ্রেণিতে বিষয় যোগ করুন');
    }
}
