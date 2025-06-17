<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ClientSettings extends Settings
{
    public ?string $default_avatar;

    public static function group(): string
    {
        return 'client';
    }

    public function defaultAvatarUrl() : string {
        return asset('storage/'.$this->default_avatar);
    }
}