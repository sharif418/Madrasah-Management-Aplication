<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookIssueResource\Pages;
use App\Models\BookIssue;
use App\Models\Book;
use App\Models\LibraryMember;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class BookIssueResource extends BaseResource
{
    protected static ?string $model = BookIssue::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-circle';

    protected static ?string $navigationGroup = 'লাইব্রেরি';

    protected static ?string $modelLabel = 'বই ইস্যু';

    protected static ?string $pluralModelLabel = 'বই ইস্যুসমূহ';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('বই ইস্যু')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('book_id')
                                    ->label('বই')
                                    ->relationship('book', 'title', fn(Builder $query) => $query->available())
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->searchable()
                                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->title} (উপলব্ধ: {$record->available_copies})"),

                                Forms\Components\Select::make('library_member_id')
                                    ->label('সদস্য')
                                    ->relationship('libraryMember', 'name', fn(Builder $query) => $query->active())
                                    ->required()
                                    ->native(false)
                                    ->preload()
                                    ->searchable()
                                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} ({$record->member_id})"),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('issue_date')
                                    ->label('ইস্যু তারিখ')
                                    ->default(now())
                                    ->required()
                                    ->native(false),

                                Forms\Components\DatePicker::make('due_date')
                                    ->label('ফেরত দেওয়ার তারিখ')
                                    ->default(now()->addDays(14))
                                    ->required()
                                    ->native(false),

                                Forms\Components\Hidden::make('issued_by')
                                    ->default(fn() => auth()->id()),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->label('বই')
                    ->searchable()
                    ->weight('bold')
                    ->limit(25),

                Tables\Columns\TextColumn::make('libraryMember.name')
                    ->label('সদস্য')
                    ->searchable()
                    ->description(fn($record) => $record->libraryMember?->member_id),

                Tables\Columns\TextColumn::make('issue_date')
                    ->label('ইস্যু')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('ফেরত তারিখ')
                    ->date('d M Y')
                    ->color(fn($record) => $record->status === 'issued' && $record->due_date < now() ? 'danger' : 'gray'),

                Tables\Columns\TextColumn::make('return_date')
                    ->label('ফেরত দিয়েছে')
                    ->date('d M Y')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('fine_amount')
                    ->label('জরিমানা')
                    ->money('BDT')
                    ->color('danger')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => BookIssue::statusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'issued' => 'warning',
                        'returned' => 'success',
                        'overdue' => 'danger',
                        'lost' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(BookIssue::statusOptions()),
            ])
            ->actions([
                Tables\Actions\Action::make('return')
                    ->label('ফেরত')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('বই ফেরত নিশ্চিত করুন')
                    ->modalDescription(fn($record) => "'{$record->book->title}' বইটি ফেরত নিতে চান?")
                    ->action(function (BookIssue $record): void {
                        $record->returnBook();

                        $message = 'বই ফেরত নেওয়া হয়েছে।';
                        if ($record->fine_amount > 0) {
                            $message .= " জরিমানা: ৳" . number_format($record->fine_amount, 2);
                        }

                        Notification::make()
                            ->success()
                            ->title('সফল!')
                            ->body($message)
                            ->send();
                    })
                    ->visible(fn(BookIssue $record): bool => $record->status === 'issued'),

                Tables\Actions\Action::make('lost')
                    ->label('হারিয়ে গেছে')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (BookIssue $record): void {
                        $record->update(['status' => 'lost']);

                        Notification::make()
                            ->warning()
                            ->title('বই হারানো হিসেবে চিহ্নিত')
                            ->send();
                    })
                    ->visible(fn(BookIssue $record): bool => $record->status === 'issued'),

                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('issue_date', 'desc')
            ->emptyStateHeading('কোন ইস্যু নেই')
            ->emptyStateIcon('heroicon-o-arrow-right-circle');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookIssues::route('/'),
            'create' => Pages\CreateBookIssue::route('/create'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $issued = static::getModel()::where('status', 'issued')->count();
        return $issued ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
