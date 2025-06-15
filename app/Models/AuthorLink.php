<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorLink extends Model
{
    use HasUuid;
    
    protected $fillable = [
        'author_id',
        'name',
        'url',
        'image_id',
        'order',
        'is_visible',
    ];

    protected static function boot() {
        parent::boot();

        static::addGlobalScope('order', function($query) {
            $query->orderBy('order');
        });
    }

    public function image() : BelongsTo {
        return $this->belongsTo(File::class, 'image_id');
    }

    public function author() : BelongsTo {
        return $this->belongsTo(Author::class, 'author_id');
    }
}
