<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KitabProgressResource\Pages;
use App\Models\KitabProgress;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KitabProgressResource extends BaseResource
{
    protected static ?string $model = KitabProgress::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'হিফজ ও কিতাব';

    protected static ?string $modelLabel = 'কিতাব প্রগ্রেস';

    protected static ?string $pluralModelLabel = 'দৈনিক কিতাব';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('কিতাব প্রগ্রেস')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('student_id')
                                    ->label('ছাত্র')
                                    ->relationship('student', 'name', fn(Builder $query) => $query->where('status', 'active'))
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\Select::make('kitab_id')
                                    ->label('কিতাব')
                                    ->relationship('kitab', 'name', fn(Builder $query) => $query->active())
                                    ->required()
                                    ->native(false)
                                    ->preload(),

                                Forms\Components\DatePicker::make('date')
                                    ->label('তারিখ')
                                    ->default(now())
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('chapter')
                                    ->label('অধ্যায়'),

                                Forms\Components\TextInput::make('lesson')
                                    ->label('দরস'),

                                Forms\Components\TextInput::make('page_from')
                                    ->label('পৃষ্ঠা থেকে')
                                    ->numeric(),

                                Forms\Components\TextInput::make('page_to')
                                    ->label('পৃষ্ঠা পর্যন্ত')
                                    ->numeric(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('teacher_id')
                                    ->label('শিক্ষক')
                                    ->relationship('teacher', 'name')
                                    ->native(false)
                                    ->preload(),

                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(KitabProgress::getStatusOptions())
                                    ->default('in_progress')
                                    ->native(false),
                            ]),

                        Forms\Components\Textarea::make('teacher_notes')
                            ->label('শিক্ষকের নোট')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('student_notes')
                            ->label('ছাত্রের নোট / মন্তব্য')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('ছাত্র')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('kitab.name')
                    ->label('কিতাব')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('chapter')
                    ->label('অধ্যায়')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('lesson')
                    ->label('দরস')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('pages_read')
                    ->label('পৃষ্ঠা')
                    ->formatStateUsing(fn($record) => $record->page_from && $record->page_to
                        ? "{$record->page_from}-{$record->page_to}"
                        : '-'),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('শিক্ষক')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => KitabProgress::getStatusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'completed' => 'success',
                        'in_progress' => 'warning',
                        'revision' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kitab_id')
                    ->label('কিতাব')
                    ->relationship('kitab', 'name')
                    ->preload(),

                Tables\Filters\SelectFilter::make('student_id')
                    ->label('ছাত্র')
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('date', 'desc')
            ->emptyStateHeading('কোন প্রগ্রেস নেই')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKitabProgress::route('/'),
            'create' => Pages\CreateKitabProgress::route('/create'),
            'edit' => Pages\EditKitabProgress::route('/{record}/edit'),
        ];
    }
}
