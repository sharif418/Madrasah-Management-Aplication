<?php

namespace App\Filament\Resources\ClassNameResource\Pages;

use App\Filament\Resources\ClassNameResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class ManageSections extends ManageRelatedRecords
{
    protected static string $resource = ClassNameResource::class;

    protected static string $relationship = 'sections';

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    public static function getNavigationLabel(): string
    {
        return 'শাখাসমূহ';
    }

    public function getTitle(): string
    {
        return $this->getRecord()->name . ' - শাখা পরিচালনা';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('শাখার নাম')
                            ->placeholder('যেমন: ক শাখা, খ শাখা')
                            ->required()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('capacity')
                            ->label('সর্বোচ্চ ছাত্র সংখ্যা')
                            ->numeric()
                            ->default(40)
                            ->minValue(1)
                            ->maxValue(100),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('class_teacher_id')
                            ->label('শ্রেণি শিক্ষক')
                            ->relationship('classTeacher', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('শিক্ষক নির্বাচন করুন'),

                        Forms\Components\TextInput::make('order')
                            ->label('ক্রম')
                            ->numeric()
                            ->default(1),
                    ]),

                Forms\Components\Toggle::make('is_active')
                    ->label('সক্রিয়')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                Tables\Columns\TextColumn::make('name')
                    ->label('শাখার নাম')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('classTeacher.name')
                    ->label('শ্রেণি শিক্ষক')
                    ->placeholder('নির্ধারণ করা হয়নি')
                    ->wrap(),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('সর্বোচ্চ ছাত্র')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('বর্তমান ছাত্র')
                    ->counts('students')
                    ->alignCenter()
                    ->badge()
                    ->color(
                        fn($record) =>
                        $record->students_count >= $record->capacity ? 'danger' : 'success'
                    ),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('স্ট্যাটাস'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('নতুন শাখা যোগ করুন'),
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
            ->emptyStateHeading('কোন শাখা নেই')
            ->emptyStateDescription('এই শ্রেণিতে শাখা যোগ করতে উপরের বাটনে ক্লিক করুন');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('শ্রেণির তালিকায় ফিরে যান')
                ->icon('heroicon-o-arrow-left')
                ->url(ClassNameResource::getUrl('index')),
        ];
    }
}
