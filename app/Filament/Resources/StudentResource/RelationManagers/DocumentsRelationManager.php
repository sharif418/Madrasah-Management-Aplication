<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'ডকুমেন্টস';

    protected static ?string $modelLabel = 'ডকুমেন্ট';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('ডকুমেন্টের ধরণ')
                    ->options([
                        'birth_certificate' => 'জন্ম সনদ',
                        'previous_certificate' => 'পূর্ববর্তী সার্টিফিকেট',
                        'photo' => 'ছবি',
                        'nid' => 'জাতীয় পরিচয়পত্র',
                        'other' => 'অন্যান্য',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('title')
                    ->label('শিরোনাম')
                    ->placeholder('ডকুমেন্টের নাম/শিরোনাম')
                    ->required()
                    ->maxLength(255),

                Forms\Components\FileUpload::make('file_path')
                    ->label('ফাইল আপলোড')
                    ->directory('students/documents')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(5120) // 5MB
                    ->required()
                    ->downloadable()
                    ->openable(),

                Forms\Components\Textarea::make('description')
                    ->label('বিবরণ')
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('ধরণ')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'birth_certificate' => 'জন্ম সনদ',
                        'previous_certificate' => 'সার্টিফিকেট',
                        'photo' => 'ছবি',
                        'nid' => 'NID',
                        'other' => 'অন্যান্য',
                        default => $state,
                    })
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('title')
                    ->label('শিরোনাম')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('আপলোড তারিখ')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('ধরণ')
                    ->options([
                        'birth_certificate' => 'জন্ম সনদ',
                        'previous_certificate' => 'সার্টিফিকেট',
                        'photo' => 'ছবি',
                        'nid' => 'NID',
                        'other' => 'অন্যান্য',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('ডকুমেন্ট যোগ করুন'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('ডাউনলোড')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('কোন ডকুমেন্ট নেই')
            ->emptyStateDescription('এই ছাত্রের জন্য ডকুমেন্ট আপলোড করুন');
    }
}
