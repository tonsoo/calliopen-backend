<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    use HasUuid;
    
    protected $fillable = [
        'uuid',
        'name',
        'color',
        'cover_id',
    ];

    public function cover() : BelongsTo {
        return $this->belongsTo(File::class, 'cover_id');
    }
}
