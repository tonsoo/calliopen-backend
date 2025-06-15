<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SongResource\Pages;
use App\Filament\Resources\SongResource\RelationManagers;
use App\Models\Album;
use App\Models\Song;
use App\Services\AudioFileService;
use App\Traits\HasCommonFields;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
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
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Storage;

class SongResource extends Resource
{
    use HasCommonFields;

    protected static ?string $model = Song::class;

    protected static ?string $navigationIcon = 'heroicon-o-musical-note';

    public static function schema() : array {
        return  [
            Fieldset::make(__('Song'))
                ->columns(1)
                ->schema([
                    TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->maxLength(255),

                    Textarea::make('lyrics')
                        ->label(__('Lyrics'))
                        ->required()
                        ->maxLength(255),

                    Toggle::make('is_explicit')
                        ->label(__('Is the song explicit?'))
                        ->default(false),

                    TextInput::make('duration_ms')
                        ->label(__('Duration'))
                        ->readOnly(),

                    FileUpload::make('file')
                        ->required()
                        ->label(__('Sound Track'))
                        ->directory(Song::UPLOAD_PATH.'raw')
                        ->acceptedFileTypes(['audio/*'])
                        ->maxSize(1024 * 1024 * 100)
                        ->afterStateUpdated(function($state, $set) {
                            if (empty($state)) {
                                $set('duration_ms', 0);
                                return;
                            } 

                            $filePath = null;
                            if ($state instanceof TemporaryUploadedFile) {
                                $filePath = $state->getRealPath();
                            } else if (is_string($state)) {
                                $diskName = config('filesystems.default');
                                $filePath = Storage::disk($diskName)->path($state);
                            } else {
                                Log::error("Unexpected state type in FileUpload afterStateUpdated: " . gettype($state));
                                $set('duration_seconds', 0.0);
                                return;
                            }

                            $duration = app(AudioFileService::class)->durationMs($filePath);
                            $set('duration_ms', max(0, $duration));
                        }),
                ]),

            static::fileUpload(__('Cover'), 'cover'),

            Fieldset::make(__('Publishing'))
                ->columns(1)
                ->schema([
                    Select::make('album_id')
                        ->label(__('Album'))
                        ->searchable()
                        ->relationship('album', 'name')
                        ->preload()
                        ->createOptionForm(AlbumResource::schema())
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} - {$record->creator->name}"),
                ])
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
                TextColumn::make('view_count')
                    ->label(__('Total views')),
                TextColumn::make('name')
                    ->label(__('Name')),
                TextColumn::make('album.name')
                    ->label(__('Album')),
                TextColumn::make('album.creator.name')
                    ->label(__('Creator')),
                IconColumn::make('is_explicit')
                    ->boolean()
                    ->label(__('Is explicit?'))
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
            'index' => Pages\ListSongs::route('/'),
            'create' => Pages\CreateSong::route('/create'),
            'edit' => Pages\EditSong::route('/{record}/edit'),
        ];
    }
}
