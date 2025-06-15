<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FileResource\Pages;
use App\Filament\Resources\FileResource\RelationManagers;
use App\Models\File;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use L1nnah\FileSize\FileSizeConverter;

class FileResource extends Resource
{
    protected static ?string $model = File::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static function  getReadableSize($size) : string {
        return (new FileSizeConverter($size))
            ->toClosestReadable()
            ->toFixed(2);
    }

    protected static function updateReadableSize($size, $set) : void {
        $converted = $size === 0 ? null : static::getReadableSize($size);
        $set('readable_size', $converted);
    }

    public static function schema() : array {
        return [
            TextInput::make('name')
                ->label(__('Name'))
                ->required()
                ->maxLength(255)
                ->helperText(__('This is the name that will be available to people when they download your file.'))
                ->visible(fn($state) => $state),
            TextInput::make('readable_size')
                ->label(__('Size'))
                ->required()
                ->readOnly()
                ->visible(fn($state) => $state)
                ->dehydrated(false)
                ->afterStateHydrated(function ($record, $set) {
                    static::updateReadableSize($record->size ?? 0, $set);
                }),
            FileUpload::make('file')
                ->label('Upload File')
                ->required()
                ->directory(File::UPLOAD_PATH)
                ->afterStateUpdated(function($state, $set) {
                    $fileSize = $state->getSize();

                    static::updateReadableSize($fileSize, $set);
                    $set('size', $fileSize);
                    $set('name', $state->getClientOriginalName());
                    $set('mime', $state->getMimeType());
                }),

            Hidden::make('size'),
            Hidden::make('mime'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema(static::schema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                
                TextColumn::make('Size')
                    ->label(__('Size'))
                    ->getStateUsing(fn($record) => static::getReadableSize($record->size ?? 0))
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(''),
            ], position: ActionsPosition::BeforeColumns)
            ->actionsPosition(ActionsPosition::BeforeColumns)
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
            'index' => Pages\ListFiles::route('/'),
            'create' => Pages\CreateFile::route('/create'),
            'edit' => Pages\EditFile::route('/{record}/edit'),
        ];
    }
}
