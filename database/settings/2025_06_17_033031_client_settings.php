<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('client', function() {
            $this->migrator->add('client.default_avatar');
        });
    }
};
