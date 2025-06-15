<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use L1nnah\FileSize\FileSizeConverter;

class File extends Model
{
    use HasUuid;

    const UPLOAD_PATH = 'uploads/files';
    
    protected $fillable = [
        'uuid',
        'name',
        'file',
        'mime',
        'size',
    ];

    public function readableSize() : string {
        return (new FileSizeConverter($this->size))
            ->toClosestReadable()
            ->toFixed(2);
    }
}
