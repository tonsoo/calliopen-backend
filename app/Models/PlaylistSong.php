<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Playlit Song
 * 
 * @property Playlist $playlist
 * @property Song $song
 * @property Client $addedBy
 * @property int $order
 */
class PlaylistSong extends Model
{
    protected $fillable = [
        'playlist_id',
        'song_id',
        'added_by_id',
        'order',
    ];

    protected static function boot() {
        parent::boot();

        static::creating(function (PlaylistSong $record) {
            if ($record->order) return;
            
            $maxOrder = static::where('playlist_id', $record->playlist_id)
                ->max('order');

            $record->order = ($maxOrder ?? 0) + 1;
        });

        static::addGlobalScope('order', function($query) {
            $query->orderBy('order');
        });
    }

    public function playlist() : BelongsTo {
        return $this->belongsTo(Playlist::class);
    }

    public function song() : BelongsTo {
        return $this->belongsTo(Song::class);
    }

    public function addedBy() : BelongsTo {
        return $this->belongsTo(Client::class, 'added_by_id');
    }
}
