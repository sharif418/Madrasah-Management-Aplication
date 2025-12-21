<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ActionGroup;

class DepartmentResource extends BaseResource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'একাডেমিক সেটআপ';

    protected static ?string $modelLabel = 'বিভাগ';

    protected static ?string $pluralModelLabel = 'বিভাগসমূহ';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('বিভাগের তথ্য')
                    ->description('মাদরাসার বিভাগের বিস্তারিত তথ্য')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('বিভাগের নাম (বাংলায়)')
                                    ->placeholder('যেমন: হিফজ বিভাগ')
                                    ->required()
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true),

                                Forms\Components\TextInput::make('name_en')
                                    ->label('বিভাগের নাম (ইংরেজিতে)')
                                    ->placeholder('e.g., Hifz Department')
                                    ->maxLength(100),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('বিবরণ')
                            ->placeholder('বিভাগ সম্পর্কে সংক্ষিপ্ত বিবরণ লিখুন...')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('head_name')
                                    ->label('বিভাগীয় প্রধান')
                                    ->placeholder('প্রধানের নাম')
                                    ->maxLength(100),

                                Forms\Components\TextInput::make('order')
                                    ->label('ক্রম')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('ছোট সংখ্যা আগে দেখাবে'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('সক্রিয়')
                                    ->default(true)
                                    ->helperText('নিষ্ক্রিয় করলে তালিকায় দেখাবে না'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->alignCenter()
                    ->width(50),

                Tables\Columns\TextColumn::make('name')
                    ->label('বিভাগের নাম')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name_en')
                    ->label('English Name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('head_name')
                    ->label('বিভাগীয় প্রধান')
                    ->searchable()
                    ->placeholder('নির্ধারণ করা হয়নি')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('classes_count')
                    ->label('শ্রেণি সংখ্যা')
                    ->counts('classes')
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('teachers_count')
                    ->label('শিক্ষক সংখ্যা')
                    ->counts('teachers')
                    ->alignCenter()
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('স্ট্যাটাস')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('তৈরির তারিখ')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('স্ট্যাটাস')
                    ->placeholder('সব')
                    ->trueLabel('সক্রিয়')
                    ->falseLabel('নিষ্ক্রিয়'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('বিভাগ মুছে ফেলুন')
                        ->modalDescription('আপনি কি নিশ্চিত এই বিভাগটি মুছে ফেলতে চান? এটি পূর্বাবস্থায় ফেরানো যাবে না।')
                        ->modalSubmitActionLabel('হ্যাঁ, মুছে ফেলুন'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->modalHeading('নির্বাচিত বিভাগগুলো মুছে ফেলুন'),
                ]),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->emptyStateHeading('কোন বিভাগ নেই')
            ->emptyStateDescription('নতুন বিভাগ যোগ করতে নিচের বাটনে ক্লিক করুন')
            ->emptyStateIcon('heroicon-o-building-office-2');
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
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}
