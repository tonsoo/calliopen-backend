<?php

namespace App\Traits;

use Str;

trait HasUuid {
    public static function bootHasUuid() : void {
        static::saving(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName() {
        return 'uuid';
    }
}
