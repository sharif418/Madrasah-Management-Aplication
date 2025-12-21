<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CircularResource\Pages;
use App\Models\Circular;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class CircularResource extends BaseResource
{
    protected static ?string $model = Circular::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'যোগাযোগ';

    protected static ?string $navigationLabel = 'সার্কুলার';

    protected static ?string $modelLabel = 'সার্কুলার';

    protected static ?string $pluralModelLabel = 'সার্কুলার';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('সার্কুলার তথ্য')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('শিরোনাম')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Select::make('target_audience')
                                    ->label('প্রাপক')
                                    ->options(Circular::getAudienceOptions())
                                    ->required()
                                    ->default('all')
                                    ->native(false),
                            ]),

                        Forms\Components\RichEditor::make('content')
                            ->label('বিষয়বস্তু')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('issue_date')
                                    ->label('জারির তারিখ')
                                    ->required()
                                    ->default(now())
                                    ->native(false),

                                Forms\Components\DatePicker::make('effective_date')
                                    ->label('কার্যকর তারিখ')
                                    ->native(false),

                                Forms\Components\Select::make('priority')
                                    ->label('গুরুত্ব')
                                    ->options(Circular::getPriorityOptions())
                                    ->default('normal')
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(Circular::getStatusOptions())
                                    ->default('draft')
                                    ->native(false),

                                Forms\Components\FileUpload::make('attachment')
                                    ->label('সংযুক্তি')
                                    ->directory('circulars')
                                    ->acceptedFileTypes(['application/pdf', 'image/*']),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('circular_no')
                    ->label('নং')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('title')
                    ->label('শিরোনাম')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('target_audience')
                    ->label('প্রাপক')
                    ->formatStateUsing(fn($state) => Circular::getAudienceOptions()[$state] ?? $state)
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('priority')
                    ->label('গুরুত্ব')
                    ->formatStateUsing(fn($state) => Circular::getPriorityOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'urgent' => 'danger',
                        'important' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('issue_date')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->formatStateUsing(fn($state) => Circular::getStatusOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(Circular::getStatusOptions()),

                Tables\Filters\SelectFilter::make('target_audience')
                    ->label('প্রাপক')
                    ->options(Circular::getAudienceOptions()),
            ])
            ->actions([
                Tables\Actions\Action::make('publish')
                    ->label('প্রকাশ')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->action(function (Circular $record) {
                        $record->update(['status' => 'published']);
                        Notification::make()->success()->title('সার্কুলার প্রকাশিত!')->send();
                    }),

                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function (Circular $record) {
                        $pdf = Pdf::loadView('pdf.circular', [
                            'circular' => $record,
                            'date' => now()->format('d/m/Y'),
                        ]);

                        return Response::streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'circular_' . $record->circular_no . '.pdf');
                    }),

                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCirculars::route('/'),
            'create' => Pages\CreateCircular::route('/create'),
            'edit' => Pages\EditCircular::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'draft')->count();
        return $count > 0 ? (string) $count : null;
    }
}
