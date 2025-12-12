<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'সেটিংস';

    protected static ?string $navigationLabel = 'রোল ব্যবস্থাপনা';

    protected static ?string $modelLabel = 'রোল';

    protected static ?string $pluralModelLabel = 'রোলসমূহ';

    protected static ?int $navigationSort = -1;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('রোলের নাম')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\Select::make('guard_name')
                                    ->label('গার্ড')
                                    ->options(Utils::getGuardOptions())
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->required(),
                                Forms\Components\Toggle::make('select_all')
                                    ->label('সব সিলেক্ট করুন')
                                    ->onIcon('heroicon-m-check')
                                    ->offIcon('heroicon-m-x-mark')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('permissions', static::getAllPermissions()->pluck('id')->toArray());
                                        } else {
                                            $set('permissions', []);
                                        }
                                    }),
                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ]),
                    ]),
                Forms\Components\Section::make('পারমিশনসমূহ')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->label('')
                            ->relationship('permissions', 'name')
                            ->bulkToggleable()
                            ->columns([
                                'default' => 1,
                                'sm' => 2,
                                'lg' => 4,
                            ])
                            ->gridDirection('row')
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('রোলের নাম')
                    ->badge()
                    ->formatStateUsing(fn($state): string => Str::headline($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('গার্ড')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('পারমিশন সংখ্যা')
                    ->counts('permissions')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('আপডেট')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    protected static function getAllPermissions()
    {
        return \Spatie\Permission\Models\Permission::all();
    }
}
