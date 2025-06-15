<?php

namespace App\Traits;

use App\Filament\Resources\FileResource;
use App\Models\File;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

trait HasCommonFields {
    public static function fileUpload(
        string $name,
        string $relationship,
    ) : Fieldset {
        return Fieldset::make($name)
            ->relationship($relationship)
            ->columns(1)
            ->schema(FileResource::schema());
    }
}