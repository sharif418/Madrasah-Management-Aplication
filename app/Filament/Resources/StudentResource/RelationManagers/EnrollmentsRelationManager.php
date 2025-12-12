<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    protected static ?string $title = 'ভর্তির ইতিহাস';

    protected static ?string $modelLabel = 'এনরোলমেন্ট';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('academic_year_id')
                    ->label('শিক্ষাবর্ষ')
                    ->relationship('academicYear', 'name')
                    ->required()
                    ->preload(),

                Forms\Components\Select::make('class_id')
                    ->label('শ্রেণি')
                    ->relationship('class', 'name')
                    ->required()
                    ->preload()
                    ->live(),

                Forms\Components\Select::make('section_id')
                    ->label('শাখা')
                    ->relationship(
                        'section',
                        'name',
                        fn($query, $get) =>
                        $query->where('class_id', $get('class_id'))
                    )
                    ->preload(),

                Forms\Components\TextInput::make('roll_no')
                    ->label('রোল নম্বর')
                    ->numeric(),

                Forms\Components\DatePicker::make('enrollment_date')
                    ->label('ভর্তির তারিখ')
                    ->default(now())
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('status')
                    ->label('স্ট্যাটাস')
                    ->options([
                        'active' => 'সক্রিয়',
                        'promoted' => 'উত্তীর্ণ',
                        'repeated' => 'পুনর্ভর্তি',
                        'transferred' => 'বদলি',
                    ])
                    ->default('active')
                    ->required()
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('academic_year_id')
            ->columns([
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('শিক্ষাবর্ষ')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('class.name')
                    ->label('শ্রেণি')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('section.name')
                    ->label('শাখা')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('roll_no')
                    ->label('রোল')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('enrollment_date')
                    ->label('ভর্তির তারিখ')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'promoted' => 'info',
                        'repeated' => 'warning',
                        'transferred' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'সক্রিয়',
                        'promoted' => 'উত্তীর্ণ',
                        'repeated' => 'পুনর্ভর্তি',
                        'transferred' => 'বদলি',
                        default => $state,
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('এনরোলমেন্ট যোগ করুন'),
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
            ->defaultSort('enrollment_date', 'desc')
            ->emptyStateHeading('কোন এনরোলমেন্ট রেকর্ড নেই');
    }
}
