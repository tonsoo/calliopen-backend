<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Album
 * @property string $uuid
 * @property string $name
 * @property File $cover
 * @property Author $creator
 * @property Song[] $songs
 */
class Album extends Model
{
    use HasUuid;
    
    protected $fillable = [
        'uuid',
        'name',
        'cover_id',
        'creator_id',
    ];

    public function cover() : BelongsTo {
        return $this->belongsTo(File::class, 'cover_id');
    }

    public function creator() : BelongsTo {
        return $this->belongsTo(Author::class, 'creator_id');
    }

    public function songs() : HasMany {
        return $this->hasMany(Song::class);
    }
}
