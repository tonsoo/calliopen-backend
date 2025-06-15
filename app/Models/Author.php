<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Author extends Model
{
    use HasUuid;
    
    protected $fillable = [
        'uuid',
        'client_id',
        'name',
        'bio',
    ];

    public function client() : BelongsTo {
        return $this->belongsTo(Client::class);
    }

    public function links() : HasMany {
        return $this->hasMany(AuthorLink::class);
    }

    public function albums() : HasMany {
        return $this->hasMany(Album::class, 'creator_id');
    }
}
