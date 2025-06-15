<?php

namespace App\Models;

use App\Traits\HasUuid;
use Exception;
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

    public static function boot() {
        parent::boot();

        static::deleting(function($model) {
            $filePath = storage_path($model->file);
            if (file_exists($filePath) && !unlink($filePath)) {
                throw new Exception('Failed to unlink file');
            }
        });
    }

    public function readableSize() : string {
        return (new FileSizeConverter($this->size))
            ->toClosestReadable()
            ->toFixed(2);
    }

    public function url() : string {
        return asset('storage/'.$this->file);
    }
}
