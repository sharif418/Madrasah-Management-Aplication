<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LibraryMemberResource\Pages;
use App\Models\LibraryMember;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class LibraryMemberResource extends BaseResource
{
    protected static ?string $model = LibraryMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'লাইব্রেরি';

    protected static ?string $modelLabel = 'সদস্য';

    protected static ?string $pluralModelLabel = 'সদস্যগণ';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('সদস্য তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('member_type')
                                    ->label('সদস্যের ধরণ')
                                    ->options(LibraryMember::memberTypeOptions())
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                Forms\Components\TextInput::make('name')
                                    ->label('নাম')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('phone')
                                    ->label('ফোন')
                                    ->tel(),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('max_books')
                                    ->label('সর্বোচ্চ বই নেওয়া যাবে')
                                    ->numeric()
                                    ->default(3)
                                    ->minValue(1),

                                Forms\Components\DatePicker::make('membership_date')
                                    ->label('সদস্যপদ তারিখ')
                                    ->default(now())
                                    ->required()
                                    ->native(false),

                                Forms\Components\DatePicker::make('expiry_date')
                                    ->label('মেয়াদ শেষ')
                                    ->native(false),
                            ]),

                        Forms\Components\Select::make('status')
                            ->label('স্ট্যাটাস')
                            ->options(LibraryMember::statusOptions())
                            ->default('active')
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member_id')
                    ->label('সদস্য আইডি')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('name')
                    ->label('নাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('member_type')
                    ->label('ধরণ')
                    ->badge()
                    ->formatStateUsing(fn($state) => LibraryMember::memberTypeOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'student' => 'info',
                        'teacher' => 'success',
                        'staff' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('phone')
                    ->label('ফোন')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('max_books')
                    ->label('সর্বোচ্চ')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('active_issues_count')
                    ->label('বর্তমান বই')
                    ->counts('activeIssues')
                    ->badge()
                    ->color('warning')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn($state) => LibraryMember::statusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'active' => 'success',
                        'expired' => 'danger',
                        'suspended' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('member_type')
                    ->label('ধরণ')
                    ->options(LibraryMember::memberTypeOptions()),

                Tables\Filters\SelectFilter::make('status')
                    ->label('স্ট্যাটাস')
                    ->options(LibraryMember::statusOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateHeading('কোন সদস্য নেই')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLibraryMembers::route('/'),
            'create' => Pages\CreateLibraryMember::route('/create'),
            'edit' => Pages\EditLibraryMember::route('/{record}/edit'),
        ];
    }
}
