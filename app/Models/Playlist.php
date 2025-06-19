<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Playlist
 * 
 * @property string $uuid
 * @property Client $creator
 * @property File $cover
 * @property string $name
 * @property int $total_duration
 * @property bool $is_public
 * @property Client[] $collaborators
 * @property PlaylistSong[] $songEntries
 * @property Song[] $songs
 */
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

    public function songEntries() : HasMany {
        return $this->hasMany(PlaylistSong::class);
    }

    public function songs() : BelongsToMany {
        return $this->belongsToMany(
            Song::class,
            'playlist_songs',
            'playlist_id',
            'song_id'
        )
        ->withPivot('added_by_id', 'order')
        ->withTimestamps();
    }
}
