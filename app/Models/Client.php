<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    use HasUuid;
    
    protected $fillable = [
        'uuid',
        'name',
        'username',
        'email',
        'password',
        'settings',
        'avatar_id',
    ];

    protected $casts = [
        'settings' => 'json'
    ];

    public function author() : HasOne {
        return $this->hasOne(Author::class, 'client_id');
    }

    public function avatar() : BelongsTo {
        return $this->belongsTo(File::class, 'avatar_id');
    }

    public function playlists() : HasMany {
        return $this->hasMany(Playlist::class, 'creator_id');
    }

    public function collaborations() : BelongsToMany {
        return $this->belongsToMany(Playlist::class, 'playlist_collaborators')
            ->withTimestamps();
    }

    public function addedSongs() : HasMany {
        return $this->hasMany(PlaylistSong::class, 'added_by_id');
    }
}
