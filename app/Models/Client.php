<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Client
 * 
 * @property string $uuid
 * @property string $name
 * @property string $username
 * @property string $email
 * @property string $password
 * @property array $settings
 * @property File $avatar
 * @property Author $author
 * @property Playlist[] $playlists
 * @property Playlist[] $collaborations
 * @property PlaylistSong[] $addedSongs
 */
class Client extends Authenticatable
{
    use HasUuid, HasApiTokens;
    
    protected $fillable = [
        'uuid',
        'name',
        'username',
        'email',
        'password',
        'settings',
        'avatar_id',
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'settings' => 'json',
        'password' => 'hashed'
    ];

    public function author() : HasOne {
        return $this->hasOne(Author::class, 'client_id');
    }

    public function avatar() : BelongsTo {
        return $this->belongsTo(File::class, 'avatar_id');
    }

    public function playlists() : HasMany {
        return $this->hasMany(Playlist::class, 'creator_id', 'id');
    }

    public function collaborations() : BelongsToMany {
        return $this->belongsToMany(Playlist::class, 'playlist_collaborators')
            ->withTimestamps();
    }

    public function addedSongs() : HasMany {
        return $this->hasMany(PlaylistSong::class, 'added_by_id');
    }
}
