<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlaylistResource\Pages;
use App\Filament\Resources\PlaylistResource\RelationManagers;
use App\Models\Playlist;
use App\Models\Song;
use App\Traits\HasCommonFields;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlaylistResource extends Resource
{
    use HasCommonFields;
    
    protected static ?string $model = Playlist::class;

    protected static ?string $navigationIcon = 'heroicon-o-play';

    public static function schema() : array {
        return [
            Select::make('creator_id')
                ->label(__('Created by'))
                ->relationship('creator', 'name')
                ->preload()
                ->searchable()
                ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} - {$record->username}"),
                
            TextInput::make('name')
                ->label(__('Name'))
                ->required()
                ->maxLength(255),

            Toggle::make('is_public')
                ->label(__('Is public?'))
                ->default(true),

            static::fileUpload(__('Cover'), 'cover'),

            Repeater::make('songs')
                ->label(__('Songs'))
                ->relationship('songs')
                ->orderColumn('order')
                ->collapsible()
                ->collapsed()
                ->itemLabel(function($state) {
                    $song = Song::find($state['song_id']);
                    if(!$song) return '-';
                    return "{$song->name} - {$song->album->creator->name}";
                })
                ->schema([
                    Select::make('song_id')
                        ->label(__('Song'))
                        ->relationship('song', 'name')
                        ->preload()
                        ->searchable()
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} - {$record->album->creator->name}"),

                    Select::make('added_id')
                        ->label(__('Added by'))
                        ->relationship('addedBy', 'name')
                        ->preload()
                        ->searchable()
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} - {$record->username}"),
                ]),
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
                    ->label(__('Name')),

                TextColumn::make('creator.name')
                    ->label(__('Created by')),

                TextColumn::make('total_duration')
                    ->label(__('Total duration')),

                IconColumn::make('is_public')
                    ->boolean()
                    ->label(__('Is public?'))
                    ->true('heroicon-o-check-circle', 'success')
                    ->false('heroicon-o-x-circle', 'danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(''),
            ], position: ActionsPosition::BeforeColumns)
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
            'index' => Pages\ListPlaylists::route('/'),
            'create' => Pages\CreatePlaylist::route('/create'),
            'edit' => Pages\EditPlaylist::route('/{record}/edit'),
        ];
    }
}
