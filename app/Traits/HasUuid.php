<?php

namespace App\Traits;

use Str;

trait HasUuid
{
    public static function bootHasUuid() : void {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
