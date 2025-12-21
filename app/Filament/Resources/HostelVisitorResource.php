<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HostelVisitorResource\Pages;
use App\Models\HostelVisitor;
use App\Models\Hostel;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HostelVisitorResource extends BaseResource
{
    protected static ?string $model = HostelVisitor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationGroup = 'হোস্টেল ও পরিবহন';

    protected static ?string $navigationLabel = 'ভিজিটর লগ';

    protected static ?string $modelLabel = 'ভিজিটর';

    protected static ?string $pluralModelLabel = 'ভিজিটর লগ';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ভিজিটর তথ্য')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('hostel_id')
                                    ->label('হোস্টেল')
                                    ->options(Hostel::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(fn(callable $set) => $set('student_id', null)),

                                Forms\Components\Select::make('student_id')
                                    ->label('ছাত্র')
                                    ->options(function (Forms\Get $get) {
                                        $hostelId = $get('hostel_id');
                                        if (!$hostelId) {
                                            return Student::where('status', 'active')
                                                ->pluck('name', 'id');
                                        }
                                        return Student::whereHas(
                                            'hostelAllocations',
                                            fn($q) =>
                                            $q->where('hostel_id', $hostelId)
                                        )->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->native(false),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('visitor_name')
                                    ->label('ভিজিটরের নাম')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('visitor_phone')
                                    ->label('মোবাইল')
                                    ->tel()
                                    ->maxLength(15),

                                Forms\Components\TextInput::make('visitor_nid')
                                    ->label('NID')
                                    ->maxLength(20),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('relation')
                                    ->label('সম্পর্ক')
                                    ->options(HostelVisitor::relationOptions())
                                    ->required()
                                    ->native(false),

                                Forms\Components\Textarea::make('purpose')
                                    ->label('আসার উদ্দেশ্য')
                                    ->rows(1),
                            ]),
                    ]),

                Forms\Components\Section::make('চেক ইন/আউট')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('check_in')
                                    ->label('প্রবেশ সময়')
                                    ->required()
                                    ->default(now())
                                    ->native(false),

                                Forms\Components\DateTimePicker::make('check_out')
                                    ->label('বের হওয়ার সময়')
                                    ->native(false),
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->label('মন্তব্য')
                            ->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('check_in')
                    ->label('তারিখ')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('ছাত্র')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('visitor_name')
                    ->label('ভিজিটর')
                    ->searchable(),

                Tables\Columns\TextColumn::make('relation')
                    ->label('সম্পর্ক')
                    ->formatStateUsing(fn($state) => HostelVisitor::relationOptions()[$state] ?? $state)
                    ->badge(),

                Tables\Columns\TextColumn::make('visitor_phone')
                    ->label('মোবাইল')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('check_in')
                    ->label('প্রবেশ')
                    ->time('h:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_out')
                    ->label('বের')
                    ->time('h:i A')
                    ->placeholder('---'),

                Tables\Columns\TextColumn::make('status')
                    ->label('অবস্থা')
                    ->badge()
                    ->color(fn($state) => $state === 'ভিতরে আছেন' ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('hostel.name')
                    ->label('হোস্টেল')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('check_in', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('hostel_id')
                    ->label('হোস্টেল')
                    ->options(Hostel::pluck('name', 'id')),

                Tables\Filters\Filter::make('today')
                    ->label('আজকের ভিজিটর')
                    ->query(fn(Builder $query) => $query->whereDate('check_in', today())),

                Tables\Filters\Filter::make('currently_inside')
                    ->label('এখনও ভিতরে')
                    ->query(fn(Builder $query) => $query->whereNull('check_out')),
            ])
            ->actions([
                Tables\Actions\Action::make('checkout')
                    ->label('চেক আউট')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->action(fn(HostelVisitor $record) => $record->update(['check_out' => now()]))
                    ->visible(fn(HostelVisitor $record) => !$record->check_out)
                    ->requiresConfirmation(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('কোন ভিজিটর নেই')
            ->emptyStateIcon('heroicon-o-user-plus');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHostelVisitors::route('/'),
            'create' => Pages\CreateHostelVisitor::route('/create'),
            'edit' => Pages\EditHostelVisitor::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        // Show currently inside visitors count
        return static::getModel()::whereNull('check_out')
            ->whereDate('check_in', today())
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
