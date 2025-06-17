<?php

namespace App\Filament\Pages;

use App\Settings\ClientSettings;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageClientSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = ClientSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                FileUpload::make('default_avatar')
                    ->label(__('Default client avatar'))
                    ->image(),
            ]);
    }
}
