<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use App\Traits\HasCommonFields;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    use HasCommonFields;

    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function schema() : array {
        return [
            Tabs::make()
                ->schema([
                    Tab::make(__('Client information'))
                        ->schema([
                            Toggle::make('is_author')
                                ->live()
                                ->label(__('Is a creator?'))
                                ->dehydrated(false)
                                ->afterStateHydrated(function ($record, $set) {
                                    $set('is_author', (bool) $record?->author);
                                }),

                            TextInput::make('name')
                                ->label(__('Name'))
                                ->required()
                                ->maxLength(255),

                            TextInput::make('username')
                                ->label(__('Username'))
                                ->unique(ignoreRecord: true)
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label(__('E-mail'))
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->email()
                                ->maxLength(255),

                            TextInput::make('password')
                                ->label(__('Password'))
                                ->required()
                                ->password()
                                ->maxLength(255),

                            self::fileUpload(__('Avatar'), 'avatar'),
                        ]),

                    Tab::make(__('Creator information'))
                        ->visible(fn($get) => $get('is_author'))
                        ->schema([
                            Group::make([
                                TextInput::make('name')
                                    ->label(__('Author name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('bio')
                                    ->label(__('Bio'))
                                    ->maxLength(255),

                                Repeater::make(__('links'))
                                    ->label(__('Links'))
                                    ->collapsible()
                                    ->collapsed()
                                    ->reorderable()
                                    ->relationship('links')
                                    ->orderColumn('order')
                                    ->itemLabel(fn($state) => $state['name'])
                                    ->schema([
                                        Hidden::make('order'),

                                        TextInput::make('name')
                                            ->label(__('Display name'))
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('url')
                                            ->label(__('Link url'))
                                            ->url()
                                            ->required()
                                            ->maxLength(255),

                                        Toggle::make('is_visible')
                                            ->label(__('Is visible?'))
                                            ->default(true),

                                        static::fileUpload(__('Image'), 'image')
                                    ])
                            ])
                                ->relationship('author')
                        ])
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
                TextColumn::make('name')
                    ->label(__('Name')),
                TextColumn::make('username')
                    ->label(__('Username')),
                TextColumn::make('email')
                    ->label(__('E-mail')),
                IconColumn::make('author')
                    ->getStateUsing(fn($record) => (bool) $record->author)
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->label(__('Is an author?')),

                TextColumn::make('author.bio')
                    ->label(__('Bio')),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    protected function mutateFormDataBeforeSave($data) {
        dd($data);
        return $data;
    }

    protected function handleRecordUpdate($record, array $data)
    {
        dd('1', $data);

        return $record;
    }
}
