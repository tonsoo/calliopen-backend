<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Playlist extends Model
{
    use HasUuid;
    
    protected $fillable = [
        'uuid',
        'creator_id',
        'cover_id',
        'name',
        'total_duration',
        'is_public',
    ];

    public function creator() : BelongsTo {
        return $this->belongsTo(Client::class, 'creator_id');
    }

    public function cover() : BelongsTo {
        return $this->belongsTo(File::class, 'cover_id');
    }

    public function collaborators() : BelongsToMany {
        return $this->belongsToMany(
            Client::class,
            'playlist_songs',
            'playlist_id',
            'added_by_id',
        )
        ->withTimestamps();
    }

    public function songs() : HasMany {
        return $this->hasMany(PlaylistSong::class);
    }
}
